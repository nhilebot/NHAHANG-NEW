<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\Category;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Khai báo mảng dữ liệu thực đơn của bạn
        $menuData = [
            // ===== 8 MÓN HẢI SẢN =====
            ['name' => 'Cua Hấp', 'price' => 250000, 'description' => 'Hải sản tươi sống hấp', 'stock' => 10, 'image' => 'images/hs1.jpg', 'category' => 'seafood'],
            ['name' => 'Tôm Càng Hấp', 'price' => 220000, 'description' => 'Tôm càng hấp thơm ngon', 'stock' => 10, 'image' => 'images/hs2.jpg', 'category' => 'seafood'],
            ['name' => 'Mực hấp', 'price' => 150000, 'description' => 'Mực tươi hấp', 'stock' => 10, 'image' => 'images/hs3.jpg', 'category' => 'seafood'],
            ['name' => 'Sò Huyết Hấp', 'price' => 120000, 'description' => 'Sò huyết hấp tươi ngon', 'stock' => 10, 'image' => 'images/hs4.jpg', 'category' => 'seafood'],
            ['name' => 'Sò điệp nướng phô mai', 'price' => 140000, 'description' => 'Sò điệp nướng béo ngậy', 'stock' => 10, 'image' => 'images/hs5.jpg', 'category' => 'seafood'],
            ['name' => 'Hàu nướng phô mai', 'price' => 100000, 'description' => 'Hàu nướng thơm ngon', 'stock' => 10, 'image' => 'images/hs6.jpg', 'category' => 'seafood'],
            ['name' => 'Nhum nướng mỡ hành', 'price' => 160000, 'description' => 'Nhum béo ngậy', 'stock' => 10, 'image' => 'images/hs7.jpg', 'category' => 'seafood'],
            ['name' => 'Sò sữa nướng bơ tỏi', 'price' => 110000, 'description' => 'Sò sữa thơm bơ tỏi', 'stock' => 10, 'image' => 'images/hs8.jpg', 'category' => 'seafood'],

            // ===== 8 MÓN ĐẶC BIỆT =====
            ['name' => 'Bánh Cuốn Hấp Lá Sen', 'price' => 120000, 'description' => 'Món đặc biệt', 'stock' => 10, 'image' => 'images/db1.jpg', 'category' => 'special'],
            ['name' => 'Sườn Nướng Quế', 'price' => 170000, 'description' => 'Sườn nướng quế là món ăn hấp dẫn với hương thơm đặc trưng của quế hòa quyện cùng vị đậm đà của sườn heo.', 'stock' => 10, 'image' => 'images/db2.jpg', 'category' => 'special'],
            ['name' => 'Tôm Càng Xốt Me', 'price' => 200000, 'description' => 'Đậm vị', 'stock' => 10, 'image' => 'images/db3.jpg', 'category' => 'special'],
            ['name' => 'Súp hải sản chua cay', 'price' => 150000, 'description' => 'Đặc biệt', 'stock' => 10, 'image' => 'images/db4.jpg', 'category' => 'special'],
            ['name' => 'Gỏi hoa chuối tôm thịt', 'price' => 110000, 'description' => 'Thanh mát', 'stock' => 10, 'image' => 'images/db5.jpg', 'category' => 'special'],
            ['name' => 'Chả giò', 'price' => 90000, 'description' => 'Truyền thống', 'stock' => 10, 'image' => 'images/db6.jpg', 'category' => 'special'],
            ['name' => 'Thịt nguội Châu Âu', 'price' => 100000, 'description' => 'Truyền thống', 'stock' => 10, 'image' => 'images/db7.jpg', 'category' => 'special'],
            ['name' => 'Ốc Bươu hấp', 'price' => 90000, 'description' => 'Ốc hấp truyền thống', 'stock' => 10, 'image' => 'images/db9.jpg', 'category' => 'special'],

            // ===== 8 MÓN SALAD =====
            ['name' => 'Salad trái cây', 'price' => 80000, 'description' => 'Salad trái cây tươi mát, tốt cho sức khỏe', 'stock' => 10, 'image' => 'images/salad1.jpg', 'category' => 'salad'],
            ['name' => 'Salad tôm xốt bơ tỏi', 'price' => 120000, 'description' => 'Tôm tươi kết hợp xốt bơ tỏi thơm ngon', 'stock' => 10, 'image' => 'images/salad2.jpg', 'category' => 'salad'],
            ['name' => 'Salad bắp','price' => 70000,'description' => 'Bắp ngọt trộn sốt thanh nhẹ','stock' => 10,'image' => 'images/salad3.jpg','category' => 'salad'],
            ['name' => 'Salad rong nho','price' => 90000,'description' => 'Rong nho giòn tươi, tốt cho sức khỏe','stock' => 10,'image' => 'images/salad4.jpg','category' => 'salad'],
            ['name' => 'Salad tôm bưởi lá é','price' => 110000,'description' => 'Tôm và bưởi kết hợp hương vị đặc biệt','stock' => 10,'image' => 'images/salad5.jpg','category' => 'salad'],
            ['name' => 'Salad nấm','price' => 85000,'description' => 'Nấm tươi trộn sốt thanh đạm','stock' => 10,'image' => 'images/salad6.jpg','category' => 'salad'],
            ['name' => 'Salad ức gà', 'price' => 95000, 'description' => 'Ức gà áp chảo kết hợp rau củ', 'stock' => 10, 'image' => 'images/salad7.jpg', 'category' => 'salad'],
            ['name' => 'Salad cá ngừ', 'price' => 115000, 'description' => 'Cá ngừ tươi trộn rau xanh', 'stock' => 10, 'image' => 'images/salad8.jpg', 'category' => 'salad'],

            // ===== 8 MÓN VIỆT =====
            ['name' => 'Món chiên', 'price' => 90000, 'description' => 'Món chiên giòn ngon', 'stock' => 10, 'image' => 'images/mv1.jpg', 'category' => 'vietnamese'],
            ['name' => 'Rau củ', 'price' => 70000, 'description' => 'Rau củ tươi ngon', 'stock' => 10, 'image' => 'images/mv2.jpg', 'category' => 'vietnamese'],
            ['name' => 'Bánh xèo', 'price' => 85000, 'description' => 'Bánh xèo giòn rụm', 'stock' => 10, 'image' => 'images/mv3.jpg', 'category' => 'vietnamese'],
            ['name' => 'Gỏi cuốn', 'price' => 80000, 'description' => 'Gỏi cuốn tươi mát', 'stock' => 10, 'image' => 'images/mv4.jpg', 'category' => 'vietnamese'],
            ['name' => 'Ốc hấp', 'price' => 95000, 'description' => 'Ốc hấp thơm ngon', 'stock' => 10, 'image' => 'images/mv5.jpg', 'category' => 'vietnamese'],
            ['name' => 'Bánh hỏi heo quay', 'price' => 120000, 'description' => 'Bánh hỏi kết hợp heo quay giòn ngon', 'stock' => 10, 'image' => 'images/mv6.jpg', 'category' => 'vietnamese'],
            ['name' => 'Phở bò', 'price' => 90000, 'description' => 'Phở bò truyền thống Việt Nam', 'stock' => 10, 'image' => 'images/mv7.jpg', 'category' => 'vietnamese'],
            ['name' => 'Bún chả', 'price' => 95000, 'description' => 'Bún chả Hà Nội thơm ngon', 'stock' => 10, 'image' => 'images/mv8.jpg', 'category' => 'vietnamese'],

            // ===== 8 MÓN TRÁNG MIỆNG =====
            ['name' => 'Kem tươi', 'price' => 50000, 'description' => 'Kem tươi mát lạnh', 'stock' => 10, 'image' => 'images/tm1.jpg', 'category' => 'dessert'],
            ['name' => 'Bánh kem', 'price' => 60000, 'description' => 'Bánh kem thơm ngon', 'stock' => 10, 'image' => 'images/tm2.jpg', 'category' => 'dessert'],
            ['name' => 'BanaCotta', 'price' => 70000, 'description' => 'BanaCotta mềm mịn', 'stock' => 10, 'image' => 'images/tm3.jpg', 'category' => 'dessert'],
            ['name' => 'Tiramisu', 'price' => 85000, 'description' => 'Bánh Ý vị cà phê béo mịn', 'stock' => 10, 'image' => 'images/tm4.jpg', 'category' => 'dessert'],    
            ['name' => 'Pudding trứng', 'price' => 50000, 'description' => 'Pudding mềm mịn thơm sữa', 'stock' => 10, 'image' => 'images/tm5.jpg', 'category' => 'dessert'],
            ['name' => 'Chè khúc bạch', 'price' => 65000, 'description' => 'Chè thanh mát, topping trái cây', 'stock' => 10, 'image' => 'images/tm6.jpg', 'category' => 'dessert'],
            ['name' => 'Trái cây dĩa', 'price' => 60000, 'description' => 'Trái cây tươi theo mùa', 'stock' => 10, 'image' => 'images/tm7.jpg', 'category' => 'dessert'],
            ['name' => 'Kem matcha', 'price' => 70000, 'description' => 'Kem vị trà xanh Nhật Bản', 'stock' => 10, 'image' => 'images/tm8.jpg', 'category' => 'dessert'],

            // ===== 4 MÓN NƯỚC UỐNG =====
            ['name' => 'Nước Cocktail Singapore Sling', 'price' => 120000, 'description' => 'Cocktail Singapore Sling thơm ngon', 'stock' => 10, 'image' => 'images/nc1.jpg', 'category' => 'drink'],
            ['name' => 'Nước Cocktail Whisky Ussina', 'price' => 120000, 'description' => 'Cocktail Whisky Ussina hảo hạng', 'stock' => 10, 'image' => 'images/nc2.jpg', 'category' => 'drink'],
            ['name' => 'Nước Mocktail Hokkaido Strawberry', 'price' => 80000, 'description' => 'Mocktail dâu Hokkaido tươi mát', 'stock' => 10, 'image' => 'images/nc3.jpg', 'category' => 'drink'],
            ['name' => 'Nước Cocktail Whisky', 'price' => 50000, 'description' => 'Whiskey Sour cocktail cổ điển bùng nổ hương vị', 'stock' => 10, 'image' => 'images/nc4.jpg', 'category' => 'drink'],
        ];

        // 2. Chạy vòng lặp để gán category_id tự động
        foreach ($menuData as $item) {
            // Tìm ID danh mục khớp với chuỗi trong cột 'category'
            $category = Category::where('name', 'LIKE', '%' . $item['category'] . '%')->first();

            $catId = null;
            if ($category) {
                $catId = $category->id;
            } else {
                // Ép ID nếu tên danh mục tiếng Việt không khớp với từ khóa tiếng Anh
                $catId = match($item['category']) {
                    'seafood'    => 1,
                    'special'    => 2,
                    'salad'      => 3,
                    'dessert'    => 4,
                    'drink'      => 5,
                    'vietnamese' => 6,
                    default      => null
                };
            }

            // 3. Lưu vào Database
            Menu::create([
                'name'        => $item['name'],
                'price'       => $item['price'],
                'description' => $item['description'],
                'stock'       => $item['stock'],
                'image'       => $item['image'],
                'category'    => $item['category'],
                'category_id' => $catId, 
            ]);
        }
    }
}