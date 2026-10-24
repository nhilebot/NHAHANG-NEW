<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Reservation;
use App\Models\ReservationItem;
use DomainException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/**
 * CỬA DUY NHẤT để thay đổi món của một lượt đặt bàn.
 *
 * Nguồn chính: bảng reservation_items (mỗi LẦN GỌI MÓN là một dòng, không bao giờ bị xóa).
 * Sau mỗi thay đổi, service tự sinh lại "bộ nhớ đệm" để các màn hình cũ vẫn chạy:
 *   - reservations.cart_data, total_price, chef_statuses
 *   - order_items (loại A: reservation_id, loại B: của hóa đơn liên kết)
 *
 * Service KHÔNG đổi reservations.status (trạng thái đơn do controller/máy trạng thái quản lý).
 * Mọi hàm thay đổi dữ liệu đều chạy trong DB::transaction và khóa dòng reservation (lockForUpdate).
 * Lỗi nghiệp vụ được ném ra dưới dạng \DomainException (message tiếng Việt, hiển thị được cho người dùng).
 *
 * Sau khi gọi, controller nên $reservation->refresh() nếu cần dùng lại model.
 */
class ReservationItemService
{
    /** Thứ hạng trạng thái bếp của một dòng */
    private const RANK = [
        ReservationItem::STATUS_PENDING => 0,
        ReservationItem::STATUS_COOKING => 1,
        ReservationItem::STATUS_DONE    => 2,
        ReservationItem::STATUS_SERVED  => 3,
    ];

    /** Trạng thái đơn coi như món đã bưng ra (dùng khi đưa dữ liệu cũ sang) */
    private const SERVED_LIKE = ['served', 'paid_cash', 'paid_transfer', 'completed'];

    private const MAX_QTY = 999;

    // =====================================================================
    //  A. GỌI MÓN
    // =====================================================================

    /**
     * Thêm một LƯỢT GỌI MÓN mới (mỗi món một dòng) vào đơn.
     * Dùng khi tạo đơn mới (khách đặt trước, nhân viên mở bàn) hoặc gọi thêm.
     *
     * @param array<int, array{menu_id?:int,id?:int,quantity:int,price?:float,name?:string}> $lines
     * @param string|null $requestKey  Khóa chống gửi trùng (ví dụ token của form). Cùng khóa + cùng món => không tạo lần hai.
     * @return Collection<int, ReservationItem> các dòng được tạo (không gồm dòng bị bỏ qua do trùng khóa)
     */
    public function addLines(
        Reservation $reservation,
        array $lines,
        string $source,
        ?int $userId = null,
        ?string $requestKey = null,
    ): Collection {
        return DB::transaction(function () use ($reservation, $lines, $source, $userId, $requestKey) {
            $res = $this->lock($reservation);
            if ($source === ReservationItem::SOURCE_STAFF_EXTRA) {
                $this->importLegacyIfNeeded($res);
            }

            $created = $this->createLines($res, $this->mergeLines($lines), $source, $userId, $requestKey, null);
            $this->syncCaches($res);

            return $created;
        });
    }

    /**
     * Đưa số lượng từng món về đúng "số phần mong muốn", chỉ ghi nhận PHẦN CHÊNH:
     *  - tăng  => tạo dòng mới (gọi thêm), các dòng cũ giữ nguyên;
     *  - giảm  => hủy bớt (tăng cancelled_quantity), ưu tiên dòng chưa chế biến, KHÔNG xóa dòng.
     *
     * Nhân viên (mặc định) chỉ giảm được phần còn ở trạng thái 'pending'.
     * Phần đã nấu/đang nấu/đã bưng chỉ hủy được khi $canCancelCooked = true (quản trị) và có lý do.
     *
     * @param array<int|string, int> $wanted  [menu_id => số phần mong muốn]. Món không có trong mảng = giữ nguyên.
     * @return array{extras: array<int,int>, reductions: array<int,int>}  phần tăng / phần giảm theo menu_id
     *         (để controller tự điều chỉnh menus.stock như trước nếu cần)
     */
    public function setQuantities(
        Reservation $reservation,
        array $wanted,
        ?int $userId = null,
        bool $canCancelCooked = false,
        ?string $reason = null,
    ): array {
        return DB::transaction(function () use ($reservation, $wanted, $userId, $canCancelCooked, $reason) {
            $res = $this->lock($reservation);
            $this->importLegacyIfNeeded($res);

            $result = $this->applyTargets($res, $wanted, ReservationItem::SOURCE_STAFF_EXTRA, $userId, $canCancelCooked, $reason, null);
            $this->syncCaches($res);

            return $result;
        });
    }

    /**
     * Đồng bộ giỏ hàng của KHÁCH khi đơn còn ở bước chờ cọc (thay cho kiểu "xóa hết rồi tạo lại").
     * Chỉ ghi nhận phần chênh; phần giảm được ghi là hủy (không xóa dòng).
     *
     * @param array<int|string, int> $wanted [menu_id => số phần] — là TOÀN BỘ giỏ: món không có trong mảng bị coi là bỏ khỏi giỏ.
     */
    public function syncCustomerCart(Reservation $reservation, array $wanted, ?int $userId = null): array
    {
        return DB::transaction(function () use ($reservation, $wanted, $userId) {
            $res = $this->lock($reservation);
            $this->importLegacyIfNeeded($res);

            // $wanted là GIỎ HÀNG ĐẦY ĐỦ: món đang có trong đơn mà không còn trong giỏ => số phần = 0.
            foreach (array_keys($this->activeQuantities($res)) as $menuId) {
                $wanted[$menuId] = $wanted[$menuId] ?? 0;
            }

            // Còn trong giai đoạn đặt trước: dùng lại batch của lần đặt đầu tiên.
            $batch = ReservationItem::where('reservation_id', $res->id)
                ->where('source', ReservationItem::SOURCE_CUSTOMER_PREORDER)->min('batch');

            $result = $this->applyTargets(
                $res, $wanted, ReservationItem::SOURCE_CUSTOMER_PREORDER, $userId, false,
                'Khách chỉnh giỏ hàng', $batch ? (int) $batch : null
            );
            $this->syncCaches($res);

            return $result;
        });
    }

    /**
     * Hủy/giảm số phần của MỘT món (theo menu_id). Dùng cho thao tác hủy món có lý do.
     */
    public function cancelMenu(
        Reservation $reservation,
        int $menuId,
        int $quantity,
        ?int $userId = null,
        bool $canCancelCooked = false,
        ?string $reason = null,
    ): void {
        DB::transaction(function () use ($reservation, $menuId, $quantity, $userId, $canCancelCooked, $reason) {
            $res = $this->lock($reservation);
            $this->importLegacyIfNeeded($res);
            $this->reduceMenu($res, $menuId, $quantity, $userId, $canCancelCooked, $reason);
            $this->syncCaches($res);
        });
    }

    // =====================================================================
    //  B. BẾP
    // =====================================================================

    /** Đánh dấu các dòng chưa gửi bếp là đã gửi (sent_at). Trả về số dòng được đánh dấu. */
    public function markSentToKitchen(Reservation $reservation): int
    {
        return DB::transaction(function () use ($reservation) {
            $res = $this->lock($reservation);
            $this->importLegacyIfNeeded($res);

            $n = ReservationItem::where('reservation_id', $res->id)->active()
                ->whereNull('sent_at')->update(['sent_at' => now()]);
            $this->syncCaches($res);

            return $n;
        });
    }

    /** Bếp bấm "Bắt đầu nấu" cả đơn: các dòng đang chờ => đang nấu. Trả về số dòng đổi. */
    public function startAll(Reservation $reservation): int
    {
        return $this->bulkMove($reservation, [ReservationItem::STATUS_PENDING], ReservationItem::STATUS_COOKING);
    }

    /** Bếp bấm "Nấu xong" cả đơn: các dòng chờ/đang nấu => xong. Trả về số dòng đổi. */
    public function finishAll(Reservation $reservation): int
    {
        return $this->bulkMove(
            $reservation,
            [ReservationItem::STATUS_PENDING, ReservationItem::STATUS_COOKING],
            ReservationItem::STATUS_DONE
        );
    }

    /** Nhân viên bưng món: các dòng đã xong => đã bưng. Trả về số dòng đổi. */
    public function serveAll(Reservation $reservation): int
    {
        return $this->bulkMove($reservation, [ReservationItem::STATUS_DONE], ReservationItem::STATUS_SERVED);
    }

    /** Đổi trạng thái đúng MỘT dòng (một lần gọi món). */
    public function setLineStatus(ReservationItem $item, string $to): ReservationItem
    {
        $this->assertStatus($to);

        return DB::transaction(function () use ($item, $to) {
            $res = $this->lock(Reservation::findOrFail($item->reservation_id));
            $line = ReservationItem::whereKey($item->id)->lockForUpdate()->firstOrFail();

            if ($line->active_quantity <= 0) {
                throw new DomainException('Dòng món này đã bị hủy hết, không thể đổi trạng thái.');
            }
            if ($line->status === ReservationItem::STATUS_SERVED && $to !== ReservationItem::STATUS_SERVED) {
                throw new DomainException('Món đã bưng ra, không thể đổi lại trạng thái bếp.');
            }

            $this->moveLine($line, $to);
            $this->syncCaches($res);

            return $line->refresh();
        });
    }

    /**
     * Tương thích route cũ theo menu_id: áp dụng cho các dòng còn hiệu lực của món đó.
     * Chỉ tiến lên (pending→cooking→done); riêng 'pending' chỉ lùi được dòng đang nấu.
     * Không bao giờ đụng vào dòng đã bưng.
     *
     * @return int số dòng đã đổi
     */
    public function setMenuStatus(Reservation $reservation, int $menuId, string $to): int
    {
        $this->assertStatus($to);

        return DB::transaction(function () use ($reservation, $menuId, $to) {
            $res = $this->lock($reservation);
            $this->importLegacyIfNeeded($res);

            $lines = ReservationItem::where('reservation_id', $res->id)->where('menu_id', $menuId)
                ->active()->orderBy('id')->lockForUpdate()->get();

            if ($lines->isEmpty()) {
                throw new DomainException('Món này không có trong đơn.');
            }

            $changed = 0;
            foreach ($lines as $line) {
                $from = self::RANK[$line->status];
                $target = self::RANK[$to];
                $forward = $target > $from;
                $backFromCooking = $to === ReservationItem::STATUS_PENDING && $line->status === ReservationItem::STATUS_COOKING;

                if ($line->status !== ReservationItem::STATUS_SERVED && ($forward || $backFromCooking)) {
                    $this->moveLine($line, $to);
                    $changed++;
                }
            }
            $this->syncCaches($res);

            return $changed;
        });
    }

    // =====================================================================
    //  C. ĐỌC / ĐỒNG BỘ
    // =====================================================================

    /** Đơn còn món chưa nấu xong (đang chờ hoặc đang nấu)? */
    public function hasUnfinished(Reservation $reservation): bool
    {
        return ReservationItem::where('reservation_id', $reservation->id)->active()
            ->whereIn('status', [ReservationItem::STATUS_PENDING, ReservationItem::STATUS_COOKING])
            ->exists();
    }

    /** Đơn cũ chưa có dòng nào: đưa món từ cart_data sang reservation_items (chạy lại an toàn). */
    public function ensureImported(Reservation $reservation): void
    {
        DB::transaction(function () use ($reservation) {
            $res = $this->lock($reservation);
            if (ReservationItem::where('reservation_id', $res->id)->exists()) {
                return;
            }
            $this->importLegacyIfNeeded($res);
            if (ReservationItem::where('reservation_id', $res->id)->exists()) {
                $this->syncCaches($res);
            }
        });
    }

    /** Số phần còn hiệu lực theo từng món: [menu_id => số phần]. */
    public function activeQuantities(Reservation $reservation): array
    {
        $out = [];
        foreach (ReservationItem::where('reservation_id', $reservation->id)->active()->get() as $l) {
            if ($l->menu_id) {
                $out[$l->menu_id] = ($out[$l->menu_id] ?? 0) + $l->active_quantity;
            }
        }

        return $out;
    }

    /** Tổng tiền các phần còn hiệu lực (làm tròn đồng). */
    public function total(Reservation $reservation): int
    {
        $sum = 0.0;
        foreach (ReservationItem::where('reservation_id', $reservation->id)->active()->get() as $l) {
            $sum += $l->line_total;
        }

        return (int) round($sum);
    }

    /**
     * Sinh lại bộ nhớ đệm từ reservation_items:
     * reservations.cart_data / total_price / chef_statuses, và order_items (loại A + hóa đơn liên kết).
     * Gọi tự động sau mỗi thay đổi; cũng có thể gọi tay để "sửa lệch".
     * Nếu đơn CHƯA có dòng nào trong reservation_items thì không làm gì (tránh xóa nhầm dữ liệu cũ).
     */
    public function syncCaches(Reservation $reservation): void
    {
        DB::transaction(function () use ($reservation) {
            $res = Reservation::whereKey($reservation->id)->lockForUpdate()->firstOrFail();
            $lines = ReservationItem::where('reservation_id', $res->id)->orderBy('batch')->orderBy('id')->get();

            if ($lines->isEmpty()) {
                return;
            }

            $images = Menu::whereIn('id', $lines->pluck('menu_id')->filter()->unique())->pluck('image', 'id');

            $total = 0.0;
            $byMenu = [];     // cho cart_data + chef_statuses
            $rowTargets = []; // cho order_items: khóa "menu|is_extra"

            foreach ($lines as $l) {
                $qty = $l->active_quantity;
                if ($qty <= 0) {
                    continue;
                }
                $total += $qty * (float) $l->unit_price;

                if (!$l->menu_id) {
                    Log::warning('reservation_items: dòng không còn menu_id nên không đưa vào cart_data.', [
                        'reservation_id' => $res->id, 'item_id' => $l->id,
                    ]);
                    continue;
                }

                $m = &$byMenu[$l->menu_id];
                $m ??= ['id' => $l->menu_id, 'name' => $l->menu_name, 'price' => (float) $l->unit_price,
                        'quantity' => 0, 'image' => $images[$l->menu_id] ?? null, 'statuses' => []];
                $m['quantity'] += $qty;
                $m['statuses'][] = $l->status;
                unset($m);

                $extra = $l->source === ReservationItem::SOURCE_STAFF_EXTRA;
                $key = $l->menu_id . '|' . (int) $extra;
                $t = &$rowTargets[$key];
                $t ??= ['menu_id' => $l->menu_id, 'product_name' => $l->menu_name, 'price' => (float) $l->unit_price,
                        'is_extra' => $extra, 'quantity' => 0, 'statuses' => []];
                $t['quantity'] += $qty;
                $t['statuses'][] = $l->status;
                unset($t);
            }

            $cart = [];
            $chef = [];
            foreach ($byMenu as $menuId => $m) {
                $cart[] = ['id' => $m['id'], 'name' => $m['name'], 'price' => $m['price'],
                           'quantity' => $m['quantity'], 'image' => $m['image']];
                $chef[$menuId] = $this->aggregateStatus($m['statuses']);
            }

            $res->cart_data = $cart;
            $res->chef_statuses = $chef;
            $res->total_price = (int) round($total);
            $res->save();

            foreach ($rowTargets as &$t) {
                $t['chef_status'] = $this->aggregateStatus($t['statuses']);
                unset($t['statuses']);
            }
            unset($t);

            // order_items loại A (gắn reservation_id, không có order_id)
            $this->syncOrderRows(
                OrderItem::where('reservation_id', $res->id)->whereNull('order_id'),
                $rowTargets,
                ['reservation_id' => $res->id, 'order_id' => null]
            );

            // order_items loại B: CHỈ hóa đơn khóa đúng reservation_id (không đoán theo user/bàn)
            $order = Order::where('reservation_id', $res->id)->latest('id')->first();
            if ($order) {
                $this->syncOrderRows(
                    OrderItem::where('order_id', $order->id),
                    $rowTargets,
                    ['order_id' => $order->id, 'reservation_id' => null]
                );
                $order->total_price = (int) round($total);
                $order->save();
            }
        });
    }

    // =====================================================================
    //  NỘI BỘ
    // =====================================================================

    private function lock(Reservation $reservation): Reservation
    {
        return Reservation::whereKey($reservation->id)->lockForUpdate()->firstOrFail();
    }

    private function assertStatus(string $status): void
    {
        if (!isset(self::RANK[$status])) {
            throw new InvalidArgumentException("Trạng thái bếp không hợp lệ: {$status}");
        }
    }

    /** Gộp các dòng cùng món trong một lần gọi, kiểm tra số lượng. */
    private function mergeLines(array $lines): array
    {
        $merged = [];
        foreach ($lines as $l) {
            $menuId = (int) ($l['menu_id'] ?? $l['id'] ?? 0);
            $qty    = (int) ($l['quantity'] ?? 0);
            if ($menuId <= 0 || $qty <= 0) {
                throw new DomainException('Dữ liệu món không hợp lệ (thiếu món hoặc số lượng phải lớn hơn 0).');
            }
            $merged[$menuId]['quantity'] = ($merged[$menuId]['quantity'] ?? 0) + $qty;
            $merged[$menuId]['price'] = $l['price'] ?? ($merged[$menuId]['price'] ?? null);
            $merged[$menuId]['name']  = $l['name'] ?? ($merged[$menuId]['name'] ?? null);
            if ($merged[$menuId]['quantity'] > self::MAX_QTY) {
                throw new DomainException('Số lượng một món quá lớn (tối đa ' . self::MAX_QTY . ' phần).');
            }
        }

        return $merged;
    }

    /**
     * Tạo dòng mới. Tên/giá lấy từ DB nếu không truyền sẵn (không tin dữ liệu client).
     * @param array<int, array{quantity:int, price:?float, name:?string}> $merged
     */
    private function createLines(
        Reservation $res,
        array $merged,
        string $source,
        ?int $userId,
        ?string $requestKey,
        ?int $forceBatch,
    ): Collection {
        if (!in_array($source, ReservationItem::SOURCES, true)) {
            throw new InvalidArgumentException("Nguồn gọi món không hợp lệ: {$source}");
        }
        if (!$merged) {
            return collect();
        }

        $menus = Menu::whereIn('id', array_keys($merged))->get()->keyBy('id');
        $created = collect();
        $batch = $forceBatch;

        foreach ($merged as $menuId => $m) {
            $menu = $menus[$menuId] ?? null;
            if (!$menu) {
                throw new DomainException("Món #{$menuId} không còn trong thực đơn.");
            }

            $key = $requestKey ? 'ri:' . substr(sha1($requestKey . '|' . $res->id . '|' . $menuId), 0, 40) : null;
            if ($key && ReservationItem::where('idempotency_key', $key)->exists()) {
                continue;   // đã xử lý lần gửi trước => bỏ qua, không tạo trùng
            }

            $price = is_numeric($m['price'] ?? null) ? (float) $m['price'] : (float) $menu->price;
            $batch ??= ((int) ReservationItem::where('reservation_id', $res->id)->max('batch')) + 1;

            $created->push(ReservationItem::create([
                'reservation_id'  => $res->id,
                'menu_id'         => $menu->id,
                'menu_name'       => $m['name'] ?: $menu->name,
                'unit_price'      => $price,
                'quantity'        => $m['quantity'],
                'batch'           => $batch,
                'source'          => $source,
                'status'          => ReservationItem::STATUS_PENDING,
                'created_by'      => $userId,
                'idempotency_key' => $key,
            ]));
        }

        return $created;
    }

    /** Đưa số phần từng món về mục tiêu, trả về phần tăng/giảm. */
    private function applyTargets(
        Reservation $res,
        array $wanted,
        string $source,
        ?int $userId,
        bool $canCancelCooked,
        ?string $reason,
        ?int $forceBatch,
    ): array {
        $current = $this->activeQuantities($res);
        $extras = [];
        $reductions = [];

        foreach ($wanted as $menuId => $newQty) {
            $menuId = (int) $menuId;
            $newQty = (int) $newQty;
            if ($newQty < 0 || $newQty > self::MAX_QTY) {
                throw new DomainException('Số lượng không hợp lệ.');
            }
            $diff = $newQty - ($current[$menuId] ?? 0);
            if ($diff > 0) {
                $extras[$menuId] = $diff;
            } elseif ($diff < 0) {
                $reductions[$menuId] = -$diff;
            }
        }

        // Giảm trước: nếu không được phép thì dừng cả giao dịch, chưa tạo gì.
        foreach ($reductions as $menuId => $qty) {
            $this->reduceMenu($res, $menuId, $qty, $userId, $canCancelCooked, $reason);
        }

        $lines = [];
        foreach ($extras as $menuId => $qty) {
            // Khách tăng số phần khi dòng đặt trước còn CHỜ và CHƯA gửi bếp: cộng vào dòng cũ
            // (chưa có gì được nấu nên không cần tách thành lần gọi mới; bếp thấy đúng một dòng).
            if ($source === ReservationItem::SOURCE_CUSTOMER_PREORDER) {
                $existing = ReservationItem::where('reservation_id', $res->id)
                    ->where('menu_id', $menuId)
                    ->where('source', ReservationItem::SOURCE_CUSTOMER_PREORDER)
                    ->where('status', ReservationItem::STATUS_PENDING)
                    ->whereNull('sent_at')
                    ->active()->orderBy('id')->lockForUpdate()->first();

                if ($existing) {
                    if ($existing->quantity + $qty > self::MAX_QTY) {
                        throw new DomainException('Số lượng một món quá lớn (tối đa ' . self::MAX_QTY . ' phần).');
                    }
                    $existing->quantity += $qty;
                    $existing->save();
                    continue;
                }
            }

            $lines[] = ['menu_id' => $menuId, 'quantity' => $qty];
        }
        $this->createLines($res, $this->mergeLines($lines), $source, $userId, null, $forceBatch);

        return ['extras' => $extras, 'reductions' => $reductions];
    }

    /** Hủy bớt $qty phần của một món. Ưu tiên dòng chưa chế biến, mới nhất trước. Không xóa dòng. */
    private function reduceMenu(
        Reservation $res,
        int $menuId,
        int $qty,
        ?int $userId,
        bool $canCancelCooked,
        ?string $reason,
    ): void {
        if ($qty <= 0) {
            throw new DomainException('Số phần cần hủy phải lớn hơn 0.');
        }

        $lines = ReservationItem::where('reservation_id', $res->id)->where('menu_id', $menuId)
            ->active()->lockForUpdate()->get()
            ->sort(fn ($a, $b) => [self::RANK[$a->status], -$a->id] <=> [self::RANK[$b->status], -$b->id])
            ->values();

        $available = $lines->sum(fn ($l) => $l->active_quantity);
        if ($qty > $available) {
            throw new DomainException("Chỉ còn {$available} phần của món này, không thể hủy {$qty} phần.");
        }

        $pendingQty = $lines->where('status', ReservationItem::STATUS_PENDING)->sum(fn ($l) => $l->active_quantity);
        $needCooked = max(0, $qty - $pendingQty);

        if ($needCooked > 0) {
            if (!$canCancelCooked) {
                throw new DomainException(
                    "Có {$needCooked} phần đã gửi bếp/đang nấu/đã nấu, nhân viên không được giảm. Vui lòng nhờ quản lý hủy kèm lý do."
                );
            }
            if (blank($reason)) {
                throw new DomainException('Hãy nhập lý do khi hủy món đã chế biến.');
            }
        }

        $reason = filled($reason) ? mb_substr($reason, 0, 255) : 'Giảm số lượng trước khi chế biến';
        $left = $qty;

        foreach ($lines as $line) {
            if ($left <= 0) {
                break;
            }
            $take = min($line->active_quantity, $left);
            $line->cancelled_quantity += $take;
            $line->cancelled_at   = now();
            $line->cancel_reason  = $reason;
            $line->cancel_stage   = $line->status;   // món đang ở giai đoạn nào khi bị hủy
            $line->cancelled_by   = $userId;
            $line->save();
            $left -= $take;
        }
    }

    private function bulkMove(Reservation $reservation, array $fromStatuses, string $to): int
    {
        return DB::transaction(function () use ($reservation, $fromStatuses, $to) {
            $res = $this->lock($reservation);
            $this->importLegacyIfNeeded($res);

            $lines = ReservationItem::where('reservation_id', $res->id)->active()
                ->whereIn('status', $fromStatuses)->lockForUpdate()->get();

            foreach ($lines as $line) {
                $this->moveLine($line, $to);
            }
            $this->syncCaches($res);

            return $lines->count();
        });
    }

    /** Đổi trạng thái một dòng và ghi mốc thời gian. Đi lùi thì xóa các mốc phía sau. */
    private function moveLine(ReservationItem $line, string $to): void
    {
        $rank = self::RANK[$to];

        $line->status = $to;
        $line->sent_at ??= now();   // đã có trạng thái bếp thì chắc chắn đã gửi bếp
        $line->cooking_at = $rank >= 1 ? ($line->cooking_at ?? now()) : null;
        $line->done_at    = $rank >= 2 ? ($line->done_at ?? now()) : null;
        $line->served_at  = $rank >= 3 ? ($line->served_at ?? now()) : null;
        $line->save();
    }

    /** Trạng thái bếp gộp theo món cho cột chef_statuses (bộ nhớ đệm cho màn hình cũ). */
    private function aggregateStatus(array $statuses): string
    {
        if (in_array(ReservationItem::STATUS_COOKING, $statuses, true)) {
            return 'cooking';
        }
        $allFinished = count($statuses) > 0 && !array_diff($statuses, [
            ReservationItem::STATUS_DONE, ReservationItem::STATUS_SERVED,
        ]);

        return $allFinished ? 'done' : 'pending';
    }

    /**
     * Cập nhật order_items theo "khóa" (menu|is_extra): sửa dòng đã có, thêm dòng thiếu,
     * và chỉ xóa dòng mà nguồn chính không còn (đây là bản sao dẫn xuất; lịch sử thật nằm ở reservation_items).
     * Không "xóa hết rồi tạo lại": dòng còn tồn tại giữ nguyên id.
     */
    private function syncOrderRows($scope, array $targets, array $createAttrs): void
    {
        $existing = $scope->orderBy('id')->get()
            ->groupBy(fn ($r) => $r->menu_id . '|' . (int) $r->is_extra);

        foreach ($targets as $key => $t) {
            $attrs = [
                'menu_id'      => $t['menu_id'],
                'product_name' => $t['product_name'],
                'quantity'     => $t['quantity'],
                'price'        => $t['price'],
                'is_extra'     => $t['is_extra'],
                'chef_status'  => $t['chef_status'],
            ];

            $rows = $existing->get($key);
            if ($rows && $rows->isNotEmpty()) {
                $rows->first()->fill($attrs)->save();
                $rows->slice(1)->each->delete();   // gộp dòng trùng khóa do dữ liệu cũ
            } else {
                OrderItem::create($attrs + $createAttrs);
            }
        }

        foreach ($existing as $key => $rows) {
            if (!isset($targets[$key])) {
                $rows->each->delete();
            }
        }
    }

    /**
     * Đơn cũ (tạo trước khi có reservation_items) mà chưa được backfill: đưa món từ cart_data sang
     * trước khi sửa, để syncCaches không ghi đè mất dữ liệu cũ. Cùng quy ước khóa với lệnh backfill
     * ('legacy:{đơn}:{số thứ tự}') nên hai nơi chạy chồng nhau vẫn không tạo trùng.
     */
    private function importLegacyIfNeeded(Reservation $res): void
    {
        if (ReservationItem::where('reservation_id', $res->id)->exists()) {
            return;
        }

        $cart = $res->cart_data;
        if (is_string($cart)) {
            $cart = json_decode($cart, true);
        }
        if (!is_array($cart) || !$cart) {
            return;
        }

        $chef = is_array($res->chef_statuses) ? $res->chef_statuses : [];
        $served = in_array($res->status, self::SERVED_LIKE, true);

        $menuIds = Menu::pluck('id')->flip();
        $byName = Menu::get(['id', 'name'])
            ->groupBy(fn ($m) => mb_strtolower(trim($m->name)))
            ->filter(fn ($g) => $g->count() === 1)->map(fn ($g) => $g->first()->id);

        foreach (array_values($cart) as $i => $it) {
            $menuId = $it['id'] ?? null;
            $qty    = (int) ($it['quantity'] ?? 0);
            $price  = $it['price'] ?? null;
            $name   = trim((string) ($it['name'] ?? ''));

            if (!$menuId && $name !== '') {
                $menuId = $byName[mb_strtolower($name)] ?? null;
            }
            if (!$menuId || $qty <= 0 || !is_numeric($price)) {
                Log::warning('reservation_items: bỏ qua dòng cart_data không hợp lệ khi đưa dữ liệu cũ sang.', [
                    'reservation_id' => $res->id, 'index' => $i,
                ]);
                continue;
            }

            $chefStatus = $chef[$menuId] ?? ($chef[(string) $menuId] ?? 'pending');
            $status = $served
                ? ReservationItem::STATUS_SERVED
                : (in_array($chefStatus, ReservationItem::STATUSES, true) ? $chefStatus : ReservationItem::STATUS_PENDING);

            ReservationItem::firstOrCreate(
                ['idempotency_key' => "legacy:{$res->id}:{$i}"],
                [
                    'reservation_id' => $res->id,
                    'menu_id'        => isset($menuIds[(int) $menuId]) ? (int) $menuId : null,
                    'menu_name'      => $name !== '' ? $name : "Món #{$menuId}",
                    'unit_price'     => (float) $price,
                    'quantity'       => $qty,
                    'batch'          => 1,
                    'source'         => ReservationItem::SOURCE_LEGACY,
                    'status'         => $status,
                ]
            );
        }
    }
}