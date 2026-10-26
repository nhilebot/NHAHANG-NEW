<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Mã khuyến mãi (mã giảm giá) áp dụng trên TIỀN MÓN ĂN (total_price của đơn/hóa đơn).
 * Đối tượng: tất cả khách hàng (không có ràng buộc theo user).
 */
class Promotion extends Model
{
    use HasFactory;

    public const TYPE_PERCENT = 'percent';
    public const TYPE_FIXED   = 'fixed';

    protected $fillable = [
        'code', 'name', 'description',
        'discount_type', 'discount_value', 'min_order_amount',
        'usage_limit', 'used_count',
        'starts_at', 'ends_at', 'is_active',
    ];

    protected $casts = [
        'discount_value'      => 'integer',
        'min_order_amount'    => 'integer',
        'usage_limit'         => 'integer',
        'used_count'          => 'integer',
        'is_active'           => 'boolean',
        'starts_at'           => 'datetime',
        'ends_at'             => 'datetime',
    ];

    /** Luôn lưu mã dạng CHỮ HOA, không khoảng trắng thừa */
    public function setCodeAttribute($value): void
    {
        $this->attributes['code'] = strtoupper(trim((string) $value));
    }

    /* ---------------------------------------------------------
     |  Trạng thái
     * --------------------------------------------------------*/

    /** active | inactive | scheduled | expired | exhausted */
    public function getStatusAttribute(): string
    {
        if (!$this->is_active)                                   return 'inactive';
        if ($this->starts_at && $this->starts_at->isFuture())    return 'scheduled';
        if ($this->ends_at && $this->ends_at->isPast())          return 'expired';
        if ($this->usage_limit !== null
            && $this->used_count >= $this->usage_limit)          return 'exhausted';

        return 'active';
    }

    public function getStatusLabelAttribute(): string
    {
        return [
            'active'    => 'Đang áp dụng',
            'inactive'  => 'Đã tắt',
            'scheduled' => 'Chưa bắt đầu',
            'expired'   => 'Hết hạn',
            'exhausted' => 'Hết lượt dùng',
        ][$this->status];
    }

    /* ---------------------------------------------------------
     |  Tính giảm giá (dùng lại ở giai đoạn 2 cho khách/thu ngân)
     * --------------------------------------------------------*/

    /**
     * Trả về lý do KHÔNG dùng được, hoặc null nếu dùng được.
     * $subtotal = tổng tiền MÓN ĂN (chưa trừ cọc, chưa trừ giảm giá).
     */
    public function unavailableReason(int $subtotal): ?string
    {
        switch ($this->status) {
            case 'inactive':  return 'Mã khuyến mãi đã bị tắt.';
            case 'scheduled': return 'Mã khuyến mãi chưa đến thời gian áp dụng.';
            case 'expired':   return 'Mã khuyến mãi đã hết hạn.';
            case 'exhausted': return 'Mã khuyến mãi đã hết lượt sử dụng.';
        }

        if ($subtotal < $this->min_order_amount) {
            return 'Đơn cần tối thiểu ' . number_format($this->min_order_amount, 0, ',', '.')
                 . 'đ tiền món để dùng mã này.';
        }

        return null;
    }

    /** Số tiền được giảm (VNĐ, số nguyên, không bao giờ vượt quá tiền món). */
    public function discountFor(int $subtotal): int
    {
        if ($subtotal <= 0) {
            return 0;
        }

        if ($this->discount_type === self::TYPE_PERCENT) {
            $discount = (int) floor($subtotal * $this->discount_value / 100);
        } else {
            $discount = (int) $this->discount_value;
        }

        return max(0, min($discount, $subtotal));
    }

    /** Chuỗi hiển thị: "10%" hoặc "50.000đ" */
    public function getDiscountTextAttribute(): string
    {
        return $this->discount_type === self::TYPE_PERCENT
            ? $this->discount_value . '%'
            : number_format($this->discount_value, 0, ',', '.') . 'đ';
    }
}
