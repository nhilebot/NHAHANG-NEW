<?php

namespace App\Console\Commands;

use App\Models\Menu;
use App\Models\OrderItem;
use App\Models\Reservation;
use App\Models\ReservationItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Chép món của các đơn đặt bàn CŨ (reservations.cart_data + chef_statuses)
 * sang bảng mới reservation_items.
 *
 * AN TOÀN:
 *  - KHÔNG sửa, KHÔNG xóa gì ở cart_data, chef_statuses, order_items, orders, reservations.
 *  - Chỉ THÊM dòng vào reservation_items.
 *  - Chạy lại nhiều lần không tạo trùng (mỗi dòng có idempotency_key 'legacy:{đơn}:{số thứ tự}').
 *  - Đơn nào ĐÃ có dòng trong reservation_items (kể cả dòng mới tạo bởi code sau này) thì bỏ qua.
 *
 * Cách dùng:
 *   php artisan reservation-items:backfill --dry-run     (chỉ báo cáo, KHÔNG ghi)
 *   php artisan reservation-items:backfill               (ghi thật)
 *   php artisan reservation-items:backfill --reservation=15
 */
class BackfillReservationItems extends Command
{
    protected $signature = 'reservation-items:backfill
                            {--dry-run : Chỉ báo cáo, không ghi gì vào database}
                            {--reservation= : Chỉ xử lý một đơn đặt bàn theo ID}';

    protected $description = 'Chép món từ reservations.cart_data sang reservation_items (không sửa/xóa dữ liệu cũ)';

    /** Trạng thái đơn coi như món đã được bưng ra */
    private const SERVED_LIKE = ['served', 'paid_cash', 'paid_transfer', 'completed'];

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $this->info($dry ? '=== CHẠY THỬ (dry-run): không ghi gì ===' : '=== GHI THẬT vào reservation_items ===');

        $menuIds = Menu::pluck('id')->flip();   // để kiểm tra menu_id còn tồn tại

        // Dữ liệu mẫu cũ có thể thiếu id món, chỉ có tên → tra theo tên.
        // Chỉ dùng khi tên khớp ĐÚNG MỘT món (tên trùng thì không đoán).
        $menuByName = Menu::get(['id', 'name'])
            ->groupBy(fn ($m) => mb_strtolower(trim($m->name)))
            ->filter(fn ($g) => $g->count() === 1)
            ->map(fn ($g) => $g->first()->id);

        $stat = [
            'reservations_seen'  => 0,
            'reservations_copy'  => 0,
            'skip_already_done'  => 0,
            'skip_no_items'      => 0,
            'lines_created'      => 0,
            'lines_invalid'      => 0,
            'lines_menu_missing' => 0,
        ];
        $issues = [];   // các đơn có dữ liệu lệch / bất thường, chỉ để báo cáo

        $query = Reservation::query()->orderBy('id');
        if ($this->option('reservation')) {
            $query->whereKey((int) $this->option('reservation'));
        }

        $query->chunkById(200, function ($chunk) use (&$stat, &$issues, $dry, $menuIds, $menuByName) {
            foreach ($chunk as $res) {
                $stat['reservations_seen']++;

                // cart_data có thể là mảng, JSON-chuỗi, hoặc rỗng
                $cart = $res->cart_data;
                if (is_string($cart)) {
                    $cart = json_decode($cart, true);
                }
                $cart = is_array($cart) ? $cart : [];

                if (count($cart) === 0) {
                    $stat['skip_no_items']++;
                    $this->noteMismatch($issues, $res, 0, 'cart_data rỗng');
                    continue;
                }

                if (ReservationItem::where('reservation_id', $res->id)->exists()) {
                    $stat['skip_already_done']++;
                    continue;
                }

                $chef = is_array($res->chef_statuses) ? $res->chef_statuses : [];
                $served = in_array($res->status, self::SERVED_LIKE, true);

                $rows = [];
                $cartTotal = 0.0;
                foreach (array_values($cart) as $i => $it) {
                    $menuId = $it['id'] ?? null;
                    $qty    = (int) ($it['quantity'] ?? 0);
                    $price  = $it['price'] ?? null;
                    $name   = trim((string) ($it['name'] ?? ''));

                    // Thiếu id nhưng có tên: tra id theo tên (chỉ khi khớp duy nhất)
                    if (!$menuId && $name !== '' && isset($menuByName[mb_strtolower($name)])) {
                        $menuId = $menuByName[mb_strtolower($name)];
                        $issues[] = "Đơn #{$res->id}: dòng " . ($i + 1) . " thiếu id món, đã khớp theo tên \"{$name}\" → menu #{$menuId}.";
                    }

                    if (!$menuId || $qty <= 0 || !is_numeric($price)) {
                        $stat['lines_invalid']++;
                        $issues[] = "Đơn #{$res->id}: dòng cart_data thứ " . ($i + 1) . ' không hợp lệ (thiếu id/số lượng/giá) → bỏ qua dòng này.';
                        continue;
                    }

                    $existsMenu = isset($menuIds[(int) $menuId]);
                    if (!$existsMenu) {
                        $stat['lines_menu_missing']++;
                        $issues[] = "Đơn #{$res->id}: món id {$menuId} không còn trong menus → lưu menu_id = NULL, giữ tên/giá cũ.";
                    }

                    $chefStatus = $chef[$menuId] ?? ($chef[(string) $menuId] ?? 'pending');
                    $status = $served
                        ? ReservationItem::STATUS_SERVED
                        : (in_array($chefStatus, ReservationItem::STATUSES, true) ? $chefStatus : ReservationItem::STATUS_PENDING);

                    $cartTotal += $qty * (float) $price;

                    $rows[] = [
                        'idempotency_key' => "legacy:{$res->id}:{$i}",
                        'reservation_id'  => $res->id,
                        'menu_id'         => $existsMenu ? (int) $menuId : null,
                        'menu_name'       => $name !== '' ? $name : "Món #{$menuId}",
                        'unit_price'      => (float) $price,
                        'quantity'        => $qty,
                        'batch'           => 1,
                        'source'          => ReservationItem::SOURCE_LEGACY,
                        'status'          => $status,
                    ];
                }

                $this->checkMismatch($issues, $res, $cartTotal);

                if (count($rows) === 0) {
                    continue;
                }

                $stat['reservations_copy']++;
                $stat['lines_created'] += count($rows);

                if (!$dry) {
                    DB::transaction(function () use ($rows) {
                        foreach ($rows as $row) {
                            // idempotency_key duy nhất → chạy lại không nhân đôi
                            ReservationItem::firstOrCreate(
                                ['idempotency_key' => $row['idempotency_key']],
                                collect($row)->except('idempotency_key')->all()
                            );
                        }
                    });
                }
            }
        });

        $this->newLine();
        $this->table(['Mục', 'Số lượng'], [
            ['Đơn đặt bàn đã xét', $stat['reservations_seen']],
            ['Đơn sẽ được chép' . ($dry ? ' (chưa ghi)' : ''), $stat['reservations_copy']],
            ['Dòng món ' . ($dry ? 'sẽ tạo' : 'đã tạo'), $stat['lines_created']],
            ['Bỏ qua: đã có dòng trong reservation_items', $stat['skip_already_done']],
            ['Bỏ qua: không có món (cart_data rỗng)', $stat['skip_no_items']],
            ['Dòng cart_data không hợp lệ (bị bỏ qua)', $stat['lines_invalid']],
            ['Dòng có món không còn trong menus', $stat['lines_menu_missing']],
        ]);

        if ($issues) {
            $this->warn('CÁC TRƯỜNG HỢP CẦN XEM LẠI (chỉ báo cáo, KHÔNG tự sửa):');
            foreach (array_slice($issues, 0, 200) as $line) {
                $this->line(' - ' . $line);
            }
            if (count($issues) > 200) {
                $this->line(' ... và ' . (count($issues) - 200) . ' mục nữa.');
            }
        } else {
            $this->info('Không phát hiện dữ liệu lệch.');
        }

        $this->info($dry ? 'Xong chạy thử. Chưa có gì bị ghi.' : 'Xong. Có thể chạy lại, sẽ không tạo trùng.');

        return self::SUCCESS;
    }

    /** So tổng cart_data với total_price và 2 loại order_items. Chỉ ghi vào danh sách báo cáo. */
    private function checkMismatch(array &$issues, Reservation $res, float $cartTotal): void
    {
        if (abs($cartTotal - (float) $res->total_price) > 0.5) {
            $issues[] = "Đơn #{$res->id}: tổng cart_data = " . number_format($cartTotal) . ' ≠ reservations.total_price = ' . number_format((float) $res->total_price) . '.';
        }

        // order_items loại A (gắn reservation_id)
        $a = (float) OrderItem::where('reservation_id', $res->id)
            ->select(DB::raw('COALESCE(SUM(quantity * price),0) as t'))->value('t');
        if ($a > 0 && abs($a - $cartTotal) > 0.5) {
            $issues[] = "Đơn #{$res->id}: order_items(reservation_id) = " . number_format($a) . ' ≠ cart_data = ' . number_format($cartTotal) . '.';
        }

        // order_items loại B (qua hóa đơn liên kết)
        $order = $res->linkedOrder();
        if ($order) {
            $b = (float) OrderItem::where('order_id', $order->id)
                ->select(DB::raw('COALESCE(SUM(quantity * price),0) as t'))->value('t');
            if (abs($b - $cartTotal) > 0.5) {
                $issues[] = "Đơn #{$res->id}: order_items(order #{$order->id}) = " . number_format($b) . ' ≠ cart_data = ' . number_format($cartTotal) . '.';
            }
        }
    }

    private function noteMismatch(array &$issues, Reservation $res, float $cartTotal, string $why): void
    {
        // Đơn đã có tiền nhưng không có món là bất thường đáng xem lại; đơn 0đ thì bình thường.
        if ((float) $res->total_price > 0) {
            $issues[] = "Đơn #{$res->id}: {$why} nhưng total_price = " . number_format((float) $res->total_price) . '.';
        }
    }
}