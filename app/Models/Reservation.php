<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    // Phần trăm cọc khi đặt bàn CÓ kèm món
const DEPOSIT_PERCENT = 20;
// Cọc cố định khi đặt bàn KHÔNG kèm món (VNĐ)
const TABLE_ONLY_DEPOSIT = 100000;

/**
 * Tính tiền cọc từ tổng tiền món.
 * - Tổng = 0 (không đặt món)  -> cọc cố định 100.000đ
 * - Tổng > 0                  -> cọc 20% tổng bill
 */
public static function calcDeposit(int $total): int
{
    if ($total <= 0) {
        return self::TABLE_ONLY_DEPOSIT;
    }
    return (int) round($total * self::DEPOSIT_PERCENT / 100);
}
    // Định nghĩa các trạng thái
    // app/Models/Reservation.php
    const STATUS_PREPARING = 'preparing'; // Đang chuẩn bị
    const STATUS_SERVED    = 'served';    // Đã lên món
    const STATUS_SERVING   = 'serving';   // Đang phục vụ
    const STATUS_COMPLETED = 'completed'; // Đã xong

    /**
     * Máy trạng thái (state machine) của đơn đặt bàn.
     * Chỉ các bước chuyển được liệt kê ở đây mới hợp lệ; mọi giá trị khác bị từ chối.
     * Lưu ý: 'served' -> 'paid_*' chỉ đi qua ReservationController::pay() (có tính tiền cọc).
     */
    public const TRANSITIONS = [
        'pending_deposit' => ['pending', 'cancelled'],   // Staff xác nhận đã nhận cọc / huỷ
        'pending'         => ['confirmed', 'cancelled'],
        'confirmed'       => ['arrived', 'cancelled'],
        'arrived'         => ['serving', 'cancelled'],   // Staff báo bếp
        'serving'         => ['ready', 'served'],        // Bếp nấu xong
        'ready'           => ['served'],                 // Staff bưng món
        'served'          => [],                         // -> thanh toán (pay)
        'paid_cash'       => ['completed'],
        'paid_transfer'   => ['completed'],
        'completed'       => [],
        'cancelled'       => [],
    ];

    public const PAID_STATUSES   = ['paid_cash', 'paid_transfer'];
    public const ACTIVE_STATUSES = ['pending_deposit', 'pending', 'confirmed', 'arrived', 'serving', 'ready', 'served', 'paid_cash', 'paid_transfer'];

    /** Trạng thái đơn đang chiếm bàn (không tính pending_deposit — xem scopeBlockingTable). */
    public const TABLE_HOLDING_STATUSES = ['pending', 'confirmed', 'arrived', 'serving', 'ready', 'served'];

    /**
     * Đơn đang GIỮ bàn: đã qua bước xác nhận cọc, HOẶC khách đã bấm
     * "xác nhận thanh toán" (payment_status = deposit_submitted) dù admin chưa duyệt cọc.
     * Đơn mới chỉ ở trang đặt bàn / giỏ hàng (chưa bấm xác nhận) KHÔNG giữ bàn.
     */
    public function scopeBlockingTable($query)
    {
        return $query->where(function ($q) {
            $q->whereIn('status', self::TABLE_HOLDING_STATUSES)
              ->orWhere(function ($q2) {
                  $q2->where('status', 'pending_deposit')
                     ->where('payment_status', 'deposit_submitted');
              });
        });
    }

    public static function canTransition(?string $from, string $to): bool
    {
        return in_array($to, self::TRANSITIONS[$from] ?? [], true);
    }

    /** Các trạng thái Staff/Admin được phép đặt thủ công qua updateStatus (không gồm paid_*). */
    public static function manualTargets(): array
    {
        $all = array_unique(array_merge(...array_values(self::TRANSITIONS)));
        return array_values(array_diff($all, self::PAID_STATUSES));
    }

    /**
     * Hoá đơn (Order) gắn với đơn đặt bàn này.
     * Ưu tiên khoá reservation_id; nếu dữ liệu cũ chưa có thì khớp chặt theo user + bàn + còn mở.
     */
    public function linkedOrder(): ?Order
    {
        return Order::where('reservation_id', $this->id)->latest()->first()
            ?? Order::where('user_id', $this->user_id)
                ->where('table_number', (string) $this->table_id)
                ->whereNotIn('status', ['paid', 'completed', 'cancelled'])
                ->latest()->first();
    }

    protected $fillable = [
    'user_id', 
    'reservation_date', 
    'reservation_time', 
    'table_id', 
    'full_name', 
    'email',
    'phone', 
    'notes', 
    'cart_data', 
    'total_price', // THÊM DÒNG NÀY (Trong ảnh DB có cột này nhưng Fillable thiếu nên ko lưu được tiền)
    'reservation_status', 
    'payment_status', 
    'payment_method', 
    'deposit_amount', 
    'cleanup_started_at', 
    'status'
];  
    protected $casts = [
        'cleanup_started_at' => 'datetime',
        'paid_at' => 'datetime',
        'deposit_paid_at' => 'datetime',
        'reservation_date' => 'date',
        'cart_data' => 'array',
         'chef_statuses'  => 'array',
    ];
   public function orderItems()
{
    // Cực kỳ quan trọng: Liên kết trực tiếp bằng reservation_id
    // Nó sẽ chỉ lấy những món ăn có gắn ID của đúng hóa đơn này
    return $this->hasMany(\App\Models\OrderItem::class, 'reservation_id', 'id');
}
public function order()
{
    // Reservation nối với Order qua user_id và table_id (hoặc reservation_id nếu có)
    return $this->hasOne(\App\Models\Order::class, 'user_id', 'user_id')
                ->where('table_number', $this->table_id)
                ->latest();
}
public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
    public function table()
    {
        // 'table_id' là tên cột khóa ngoại trong bảng reservations nối với bảng tables
        return $this->belongsTo(\App\Models\Table::class, 'table_id');
    }
}   