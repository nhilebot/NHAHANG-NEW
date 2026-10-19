<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\IngredientCategory;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

/**
 * Dữ liệu MẪU cho kho nguyên liệu (nhóm, nhà cung cấp, nguyên liệu theo thực đơn Aurora Garden).
 * Chạy lại nhiều lần vẫn an toàn: bản ghi đã có (trùng tên) sẽ được bỏ qua, không ghi đè, không nhân đôi.
 *
 *   php artisan db:seed --class=InventorySampleSeeder
 */
class InventorySampleSeeder extends Seeder
{
    public function run(): void
    {
        // ---------- Nhóm nguyên liệu ----------
        $categories = [
            'Hải sản'               => 'Cua, tôm, mực, sò, hàu...',
            'Thịt & gia cầm'        => 'Heo, bò, gà, thịt nguội',
            'Rau củ & trái cây'     => 'Rau, củ, quả, nấm',
            'Tinh bột'              => 'Bún, phở, bánh, bột',
            'Gia vị & phụ liệu'     => 'Gia vị, dầu ăn, bơ, phô mai',
            'Nguyên liệu tráng miệng' => 'Trứng, sữa, kem, bột làm bánh',
            'Đồ uống'               => 'Rượu, soda, nguyên liệu pha chế',
        ];
        $catId = [];
        foreach ($categories as $name => $desc) {
            $catId[$name] = IngredientCategory::firstOrCreate(['name' => $name], ['description' => $desc])->id;
        }

        // ---------- Nhà cung cấp (thông tin giả, chỉ để thử) ----------
        $suppliers = [
            ['Chợ đầu mối hải sản',  '0900000001', 'haisan@example.com',  'Khu chợ đầu mối, TP.HCM'],
            ['Cơ sở thịt sạch An Phát', '0900000002', 'thitsach@example.com', 'Quận Bình Tân, TP.HCM'],
            ['Rau củ Đà Lạt Xanh',   '0900000003', 'raucu@example.com',   'Đà Lạt, Lâm Đồng'],
            ['Đại lý đồ uống Minh Khang', '0900000004', 'douong@example.com', 'Quận 7, TP.HCM'],
        ];
        foreach ($suppliers as [$name, $phone, $email, $address]) {
            if (!Supplier::where('name', $name)->exists()) {
                Supplier::create([
                    'code' => Supplier::newCode(), 'name' => $name, 'phone' => $phone,
                    'email' => $email, 'address' => $address, 'is_active' => true,
                ]);
            }
        }

        // ---------- Nguyên liệu ----------
        // [tên, nhóm, đơn vị, tồn mẫu, mức tối thiểu, giá nhập tham khảo]
        $rows = [
            // Hải sản
            ['Cua biển',   'Hải sản', 'kg', 15, 5, 450000],
            ['Tôm càng',   'Hải sản', 'kg', 15, 5, 380000],
            ['Tôm thẻ',    'Hải sản', 'kg', 10, 3, 220000],
            ['Mực tươi',   'Hải sản', 'kg', 10, 3, 250000],
            ['Sò huyết',   'Hải sản', 'kg', 12, 4, 120000],
            ['Sò điệp',    'Hải sản', 'kg', 8,  3, 300000],
            ['Hàu',        'Hải sản', 'kg', 10, 3, 180000],
            ['Nhum biển',  'Hải sản', 'kg', 2,  2, 400000],   // sắp hết (để thử cảnh báo)
            ['Sò sữa',     'Hải sản', 'kg', 8,  3, 130000],
            ['Cá ngừ',     'Hải sản', 'kg', 6,  2, 280000],
            ['Ốc bươu',    'Hải sản', 'kg', 8,  3, 90000],

            // Thịt & gia cầm
            ['Sườn heo',           'Thịt & gia cầm', 'kg', 10, 4,   180000],
            ['Heo quay',           'Thịt & gia cầm', 'kg', 8,  3,   220000],
            ['Thịt heo xay',       'Thịt & gia cầm', 'kg', 10, 4,   130000],
            ['Thịt bò (phở)',      'Thịt & gia cầm', 'kg', 10, 4,   260000],
            ['Ức gà',              'Thịt & gia cầm', 'kg', 8,  3,   95000],
            ['Thịt nguội Châu Âu', 'Thịt & gia cầm', 'kg', 4,  1.5, 450000],

            // Rau củ & trái cây
            ['Hoa chuối',              'Rau củ & trái cây', 'kg', 4,  1.5, 40000],
            ['Rong nho',               'Rau củ & trái cây', 'kg', 0.5, 1,  200000],  // sắp hết
            ['Bắp ngọt',               'Rau củ & trái cây', 'kg', 6,  2,   35000],
            ['Nấm các loại',           'Rau củ & trái cây', 'kg', 5,  2,   90000],
            ['Bưởi',                   'Rau củ & trái cây', 'kg', 5,  2,   45000],
            ['Rau xà lách, rau thơm',  'Rau củ & trái cây', 'kg', 8,  3,   50000],
            ['Rau củ tươi',            'Rau củ & trái cây', 'kg', 10, 4,   40000],
            ['Trái cây theo mùa',      'Rau củ & trái cây', 'kg', 10, 4,   60000],
            ['Dâu Hokkaido',           'Rau củ & trái cây', 'kg', 3,  1,   350000],

            // Tinh bột
            ['Bánh phở',                 'Tinh bột', 'kg',  8,  3,   35000],
            ['Bún tươi',                 'Tinh bột', 'kg',  8,  3,   25000],
            ['Bánh hỏi',                 'Tinh bột', 'kg',  4,  1.5, 45000],
            ['Bột bánh xèo / bánh cuốn', 'Tinh bột', 'kg',  6,  2,   40000],
            ['Bánh tráng',               'Tinh bột', 'gói', 20, 6,   25000],

            // Gia vị & phụ liệu
            ['Phô mai Mozzarella', 'Gia vị & phụ liệu', 'kg',  4,  1.5, 280000],
            ['Bơ lạt',             'Gia vị & phụ liệu', 'kg',  4,  1.5, 220000],
            ['Tỏi',                'Gia vị & phụ liệu', 'kg',  4,  1.5, 60000],
            ['Hành lá',            'Gia vị & phụ liệu', 'kg',  3,  1,   40000],
            ['Me',                 'Gia vị & phụ liệu', 'kg',  2,  0.5, 50000],
            ['Quế',                'Gia vị & phụ liệu', 'kg',  0,  0.3, 250000],         // hết hàng
            ['Nước mắm',           'Gia vị & phụ liệu', 'lít', 10, 3,   60000],
            ['Dầu ăn',             'Gia vị & phụ liệu', 'lít', 20, 6,   45000],
            ['Đường',              'Gia vị & phụ liệu', 'kg',  10, 3,   22000],

            // Nguyên liệu tráng miệng
            ['Trứng gà',            'Nguyên liệu tráng miệng', 'quả', 150, 50,  3500],
            ['Sữa tươi',            'Nguyên liệu tráng miệng', 'lít', 20,  6,   35000],
            ['Kem tươi (whipping)', 'Nguyên liệu tráng miệng', 'lít', 8,   3,   120000],
            ['Kem vani',            'Nguyên liệu tráng miệng', 'kg',  6,   2,   110000],
            ['Mascarpone',          'Nguyên liệu tráng miệng', 'kg',  3,   1,   300000],
            ['Bột matcha',          'Nguyên liệu tráng miệng', 'kg',  0.3, 0.3, 900000], // sắp hết (chạm mức tối thiểu)
            ['Cà phê',              'Nguyên liệu tráng miệng', 'kg',  3,   1,   250000],

            // Đồ uống
            ['Whisky', 'Đồ uống', 'chai', 6,  2,   650000],
            ['Gin',    'Đồ uống', 'chai', 5,  2,   550000],
            ['Soda',   'Đồ uống', 'lon',  48, 12,  9000],
            ['Chanh',  'Đồ uống', 'kg',   4,  1.5, 40000],
        ];

        foreach ($rows as [$name, $category, $unit, $stock, $min, $price]) {
            if (Ingredient::where('name', $name)->exists()) {
                continue;   // đã có thì giữ nguyên, không ghi đè số liệu thật
            }

            $ingredient = Ingredient::create([
                'code'            => Ingredient::newCode(),
                'name'            => $name,
                'category_id'     => $catId[$category],
                'unit'            => $unit,
                'min_stock'       => $min,
                'reference_price' => $price,
                'is_active'       => true,
            ]);

            // current_stock không nằm trong $fillable (chỉ nghiệp vụ kho mới được đổi),
            // nên dữ liệu mẫu gán trực tiếp. Đây là tồn đầu kỳ GIẢ LẬP để thử giao diện.
            $ingredient->forceFill(['current_stock' => $stock])->save();
        }
    }
}