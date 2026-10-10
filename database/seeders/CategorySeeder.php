<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // ✅ QUAN TRỌNG

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories')->insertOrIgnore([
            ['id' => 1, 'name' => 'Hải sản', 'image' => 'images/categories/seafood.jpg', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Món đặc biệt', 'image' => 'images/categories/special.jpg', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Salad', 'image' => 'images/categories/salad.jpg', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'Tráng miệng', 'image' => 'images/categories/desserts.jpg', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'name' => 'Đồ uống', 'image' => 'images/categories/drinks.jpg', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'name' => 'Món Việt', 'image' => 'images/categories/vietnamese.jpg', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}