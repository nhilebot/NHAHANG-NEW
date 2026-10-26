<?php

namespace Database\Seeders;

use App\Models\Promotion;
use Illuminate\Database\Seeder;

/**
 * Khuyến mãi mẫu cho đủ 5 trạng thái: Đang áp dụng, Chưa bắt đầu, Hết hạn, Đã tắt, Hết lượt dùng.
 * Mốc thời gian tính theo NGÀY HIỆN TẠI nên chạy lúc nào trạng thái cũng đúng.
 *
 * Chạy:  php artisan db:seed --class=PromotionSeeder
 * An toàn khi chạy lại: không tạo trùng mã (dựa trên cột code) và không xóa dữ liệu khác.
 * Số lượt đã dùng (used_count) của mã đang chạy chỉ được đặt khi tạo mới, chạy lại sẽ không ghi đè.
 */
class PromotionSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $samples = [
            // ───────────── ĐANG ÁP DỤNG ─────────────
            [
                'code' => 'WELCOME10', 'name' => 'Chào bạn mới giảm 10%',
                'description' => 'Giảm 10% tiền món, không yêu cầu tối thiểu, không giới hạn thời gian.',
                'discount_type' => 'percent', 'discount_value' => 10,
                'min_order_amount' => 0, 'usage_limit' => null, 'used_count' => 0,
                'starts_at' => null, 'ends_at' => null, 'is_active' => true,
            ],
            [
                'code' => 'FIX50K', 'name' => 'Giảm 50.000đ cho đơn từ 300.000đ',
                'description' => 'Giảm số tiền cố định 50.000đ.',
                'discount_type' => 'fixed', 'discount_value' => 50000,
                'min_order_amount' => 300000, 'usage_limit' => null, 'used_count' => 0,
                'starts_at' => null, 'ends_at' => null, 'is_active' => true,
            ],
            [
                'code' => 'WEEKEND20', 'name' => 'Cuối tuần giảm 20%',
                'description' => 'Giảm 20% cho đơn từ 400.000đ, đang trong thời gian áp dụng (còn 30 ngày).',
                'discount_type' => 'percent', 'discount_value' => 20,
                'min_order_amount' => 400000, 'usage_limit' => null, 'used_count' => 0,
                'starts_at' => $now->copy()->subDays(3), 'ends_at' => $now->copy()->addDays(30), 'is_active' => true,
            ],
            [
                'code' => 'VIP100K', 'name' => 'Khách thân thiết giảm 100.000đ',
                'description' => 'Giảm 100.000đ cho đơn từ 800.000đ, giới hạn 100 lượt (đã dùng 12).',
                'discount_type' => 'fixed', 'discount_value' => 100000,
                'min_order_amount' => 800000, 'usage_limit' => 100, 'used_count' => 12,
                'starts_at' => null, 'ends_at' => $now->copy()->addDays(60), 'is_active' => true,
            ],

            // ───────────── CHƯA BẮT ĐẦU ─────────────
            [
                'code' => 'SOON20', 'name' => 'Khuyến mãi sắp diễn ra',
                'description' => 'Bắt đầu sau 7 ngày, kéo dài 30 ngày.',
                'discount_type' => 'percent', 'discount_value' => 20,
                'min_order_amount' => 200000, 'usage_limit' => null, 'used_count' => 0,
                'starts_at' => $now->copy()->addDays(7), 'ends_at' => $now->copy()->addDays(37), 'is_active' => true,
            ],

            // ───────────── HẾT HẠN ─────────────
            [
                'code' => 'EXPIRED10', 'name' => 'Khuyến mãi đã kết thúc',
                'description' => 'Kết thúc cách đây 10 ngày.',
                'discount_type' => 'percent', 'discount_value' => 10,
                'min_order_amount' => 0, 'usage_limit' => null, 'used_count' => 35,
                'starts_at' => $now->copy()->subDays(40), 'ends_at' => $now->copy()->subDays(10), 'is_active' => true,
            ],

            // ───────────── ĐÃ TẮT ─────────────
            [
                'code' => 'PAUSED15', 'name' => 'Khuyến mãi tạm dừng',
                'description' => 'Admin đã tắt thủ công (vẫn còn hạn nhưng không dùng được).',
                'discount_type' => 'percent', 'discount_value' => 15,
                'min_order_amount' => 150000, 'usage_limit' => null, 'used_count' => 4,
                'starts_at' => null, 'ends_at' => $now->copy()->addDays(20), 'is_active' => false,
            ],

            // ───────────── HẾT LƯỢT DÙNG ─────────────
            [
                'code' => 'SOLDOUT', 'name' => 'Khuyến mãi đã hết lượt',
                'description' => 'Giới hạn 50 lượt, đã dùng đủ 50.',
                'discount_type' => 'percent', 'discount_value' => 10,
                'min_order_amount' => 0, 'usage_limit' => 50, 'used_count' => 50,
                'starts_at' => null, 'ends_at' => null, 'is_active' => true,
                '_force_used' => true,   // luôn đặt lại để mã này luôn ở trạng thái "hết lượt"
            ],
        ];

        foreach ($samples as $row) {
            $forceUsed = $row['_force_used'] ?? false;
            unset($row['_force_used']);

            $promo = Promotion::firstOrNew(['code' => $row['code']]);
            $keepUsed = $promo->exists && !$forceUsed ? $promo->used_count : null;

            $promo->fill($row);
            if ($keepUsed !== null) {
                $promo->used_count = $keepUsed;   // không ghi đè lượt dùng thật của mã đang chạy
            }
            $promo->save();
        }

        $this->command?->info('Đã tạo/cập nhật ' . count($samples) . ' khuyến mãi mẫu (đủ 5 trạng thái).');
    }
}