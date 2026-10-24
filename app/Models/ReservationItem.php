<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Một LẦN GỌI MÓN trong một lượt đặt bàn. Đây là nguồn dữ liệu chính của món.
 * Không xóa dòng khi giảm/hủy món: chỉ tăng cancelled_quantity.
 */
class ReservationItem extends Model
{
    // Trạng thái bếp của từng dòng
    public const STATUS_PENDING = 'pending';
    public const STATUS_COOKING = 'cooking';
    public const STATUS_DONE    = 'done';
    public const STATUS_SERVED  = 'served';

    public const STATUSES = [
        self::STATUS_PENDING, self::STATUS_COOKING, self::STATUS_DONE, self::STATUS_SERVED,
    ];

    // Nguồn của lần gọi món
    public const SOURCE_CUSTOMER_PREORDER = 'customer_preorder';
    public const SOURCE_STAFF_WALKIN      = 'staff_walkin';
    public const SOURCE_STAFF_EXTRA       = 'staff_extra';
    public const SOURCE_LEGACY            = 'legacy';   // dữ liệu cũ được đưa sang bằng lệnh backfill

    public const SOURCES = [
        self::SOURCE_CUSTOMER_PREORDER, self::SOURCE_STAFF_WALKIN,
        self::SOURCE_STAFF_EXTRA, self::SOURCE_LEGACY,
    ];

    protected $fillable = [
        'reservation_id', 'menu_id', 'menu_name', 'unit_price',
        'quantity', 'cancelled_quantity', 'batch', 'source', 'status',
        'sent_at', 'cooking_at', 'done_at', 'served_at',
        'cancelled_at', 'cancel_reason', 'cancel_stage', 'cancelled_by',
        'created_by', 'idempotency_key',
    ];

    protected $casts = [
        'unit_price'         => 'decimal:2',
        'quantity'           => 'integer',
        'cancelled_quantity' => 'integer',
        'batch'              => 'integer',
        'sent_at'            => 'datetime',
        'cooking_at'         => 'datetime',
        'done_at'            => 'datetime',
        'served_at'          => 'datetime',
        'cancelled_at'       => 'datetime',
    ];

    public function reservation() { return $this->belongsTo(Reservation::class); }
    public function menu()        { return $this->belongsTo(Menu::class); }
    public function creator()     { return $this->belongsTo(User::class, 'created_by'); }
    public function canceller()   { return $this->belongsTo(User::class, 'cancelled_by'); }

    /** Số phần còn hiệu lực = đã gọi - đã hủy */
    public function getActiveQuantityAttribute(): int
    {
        return max(0, (int) $this->quantity - (int) $this->cancelled_quantity);
    }

    /** Thành tiền của phần còn hiệu lực */
    public function getLineTotalAttribute(): float
    {
        return $this->active_quantity * (float) $this->unit_price;
    }

    /** Chỉ lấy các dòng còn ít nhất 1 phần hiệu lực */
    public function scopeActive($q)
    {
        return $q->whereColumn('cancelled_quantity', '<', 'quantity');
    }
}