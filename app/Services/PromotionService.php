<?php

namespace App\Services;

use App\Models\Promotion;
use App\Models\Reservation;

/**
 * Gắn / gỡ / chốt mã khuyến mãi cho một đặt bàn (hóa đơn).
 *
 * Nguyên tắc:
 *  - "Gắn mã" chỉ ghi promotion_id + promotion_code, KHÔNG trừ tiền, KHÔNG tăng lượt dùng.
 *  - Số tiền giảm được tính 1 lần duy nhất lúc thanh toán (settle) từ total_price cuối cùng.
 *  - total_price (tiền món gốc) và deposit_amount (cọc) không bao giờ bị sửa ở đây.
 */
class PromotionService
{
    /** Gắn mã vào đơn. $subtotal = tổng tiền món hiện tại. Ném DomainException nếu không dùng được. */
    public function attach(Reservation $reservation, string $code, int $subtotal): Promotion
    {
        $code = strtoupper(trim($code));
        $promo = Promotion::where('code', $code)->first();

        if (!$promo) {
            throw new \DomainException('Mã khuyến mãi không tồn tại.');
        }
        if ($subtotal <= 0) {
            throw new \DomainException('Đơn chưa có món nên chưa áp dụng được mã khuyến mãi.');
        }
        if ($reason = $promo->unavailableReason($subtotal)) {
            throw new \DomainException($reason);
        }

        $reservation->promotion_id   = $promo->id;
        $reservation->promotion_code = $promo->code;
        $reservation->save();

        return $promo;
    }

    /**
     * Các mã đang chạy (bật, trong thời gian, còn lượt) để khách chọn ở giỏ hàng.
     * Mã chưa đủ điều kiện "tiền món tối thiểu" vẫn có trong danh sách để hiện yêu cầu tối thiểu.
     */
    public function listActive(): array
    {
        return Promotion::query()
            ->where('is_active', true)
            ->where(fn ($w) => $w->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($w) => $w->whereNull('ends_at')->orWhere('ends_at', '>=', now()))
            ->where(fn ($w) => $w->whereNull('usage_limit')->orWhereColumn('used_count', '<', 'usage_limit'))
            ->orderBy('min_order_amount')->orderBy('code')
            ->get()
            ->map(fn (Promotion $p) => [
                'code'  => $p->code,
                'name'  => $p->name,
                'type'  => $p->discount_type,
                'value' => (int) $p->discount_value,
                'min'   => (int) $p->min_order_amount,
            ])
            ->all();
    }

    public function detach(Reservation $reservation): void
    {
        $reservation->promotion_id   = null;
        $reservation->promotion_code = null;
        $reservation->save();
    }

    /**
     * Xem trước (chỉ đọc): ['promotion' => ?Promotion, 'discount' => int, 'error' => ?string]
     * Dùng cho trang giỏ hàng và trang thanh toán.
     */
    public function summary(Reservation $reservation, int $subtotal): array
    {
        $promo = $reservation->promotion_id ? Promotion::find($reservation->promotion_id) : null;

        if (!$promo) {
            return ['promotion' => null, 'discount' => 0, 'error' => null];
        }

        $error = $promo->unavailableReason($subtotal);

        return [
            'promotion' => $promo,
            'discount'  => $error ? 0 : $promo->discountFor($subtotal),
            'error'     => $error,
        ];
    }

    /**
     * CHỐT giảm giá lúc thanh toán. PHẢI gọi bên trong DB::transaction
     * (ReservationController::pay đã có sẵn transaction + lockForUpdate trên reservation).
     * Khóa dòng khuyến mãi để 2 hóa đơn không cùng lấy lượt cuối cùng.
     * Trả về số tiền giảm (0 nếu đơn không gắn mã).
     */
    public function settle(Reservation $reservation, int $subtotal): int
    {
        if (!$reservation->promotion_id) {
            return 0;
        }

        $promo = Promotion::lockForUpdate()->find($reservation->promotion_id);
        if (!$promo) {
            throw new \DomainException('Mã khuyến mãi gắn với đơn không còn tồn tại. Vui lòng gỡ mã rồi thanh toán lại.');
        }

        if ($reason = $promo->unavailableReason($subtotal)) {
            throw new \DomainException('Mã ' . $promo->code . ' không dùng được: ' . $reason
                . ' Vui lòng gỡ mã ở màn hình thanh toán rồi thanh toán lại.');
        }

        $discount = $promo->discountFor($subtotal);
        if ($discount > 0) {
            $promo->increment('used_count');
        }

        return $discount;
    }
}
