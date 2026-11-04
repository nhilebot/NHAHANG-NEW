<?php

namespace App\Support;

use App\Models\Menu;
use App\Models\OrderItem;
use App\Models\Reservation;
use App\Models\ReservationItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Món bán chạy nhất = tổng số phần của các đơn mà khách ĐÃ THANH TOÁN phần còn lại.
 *
 *  (1) Đơn đặt bàn: Reservation::pay() chuyển status -> paid_cash / paid_transfer (rồi completed).
 *      Món lấy từ reservation_items, số lượng = quantity - cancelled_quantity (món đã hủy không tính).
 *  (2) Đơn lẻ không gắn đặt bàn (reservation_id = NULL): tính khi status = paid / completed.
 *      Đơn gắn đặt bàn KHÔNG lấy từ order_items vì đó chỉ là bản sao của reservation_items (tránh cộng 2 lần).
 *
 * Dùng chung cho trang khách (menu) và có thể dùng cho trang admin.
 */
class BestSellers
{
    /** Thời gian lưu cache xếp hạng cho trang khách (giây). Tồn kho/ẩn món luôn lấy mới, chỉ xếp hạng được cache. */
    public const CACHE_SECONDS = 60;

    /**
     * Xếp hạng [menu_id => số phần đã bán], giảm dần.
     */
    public static function ranking(int $limit = 12, bool $fresh = false): array
    {
        // $fresh = true: tính trực tiếp từ database, không dùng cache (trang admin dùng cách này)
        if ($fresh) {
            return self::compute($limit);
        }

        return Cache::remember("best_sellers_ranking_{$limit}", self::CACHE_SECONDS, fn () => self::compute($limit));
    }

    /** Xóa cache xếp hạng (gọi khi vừa thanh toán xong nếu muốn trang khách cập nhật ngay). */
    public static function clearCache(): void
    {
        foreach ([5, 12, 15, 18, 24] as $n) {
            Cache::forget("best_sellers_ranking_{$n}");
        }
    }

    /** Truy vấn thật sự: [menu_id => số phần đã bán], giảm dần. */
    private static function compute(int $limit): array
    {
        $paidReservation = array_merge(Reservation::PAID_STATUSES, ['completed']);

        $fromReservations = ReservationItem::query()
            ->join('reservations', 'reservations.id', '=', 'reservation_items.reservation_id')
            ->whereIn('reservations.status', $paidReservation)
            ->whereNotNull('reservation_items.menu_id')
            ->groupBy('reservation_items.menu_id')
            ->selectRaw('reservation_items.menu_id as menu_id,
                         SUM(reservation_items.quantity - reservation_items.cancelled_quantity) as qty');

        $fromOrders = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereNull('orders.reservation_id')
            ->whereIn('orders.status', ['paid', 'completed'])
            ->whereNotNull('order_items.menu_id')
            ->groupBy('order_items.menu_id')
            ->selectRaw('order_items.menu_id as menu_id, SUM(order_items.quantity) as qty');

        return DB::query()
            ->fromSub($fromReservations->unionAll($fromOrders), 'sold')
            ->selectRaw('menu_id, SUM(qty) as qty')
            ->groupBy('menu_id')
            ->havingRaw('SUM(qty) > 0')
            ->orderByDesc('qty')
            ->limit($limit)
            ->pluck('qty', 'menu_id')
            ->map(fn ($q) => (int) $q)
            ->all();
    }

    /**
     * Các món bán chạy để hiển thị cho khách: chỉ món đang bán (status = 1),
     * mỗi món có thêm thuộc tính ->sold_qty. Món đã xóa/ẩn bị bỏ qua.
     */
    public static function top(int $limit = 4, bool $fresh = false): Collection
    {
        $ranking = self::ranking($limit * 3, $fresh);      // lấy dư để bù cho món bị ẩn
        if (!$ranking) {
            return collect();
        }

        $menus = Menu::with('category_relation')
            ->where('status', 1)
            ->whereIn('id', array_keys($ranking))
            ->get()
            ->keyBy('id');

        return collect($ranking)
            ->map(function ($qty, $menuId) use ($menus) {
                $menu = $menus->get($menuId);
                if ($menu) {
                    $menu->sold_qty = $qty;
                }
                return $menu;
            })
            ->filter()
            ->take($limit)
            ->values();
    }
}
