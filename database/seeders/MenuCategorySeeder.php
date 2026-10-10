<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    // Tìm danh mục Seafood trong bảng categories
    $category = \App\Models\Category::where('name', 'LIKE', '%seafood%')->first();

    if ($category) {
        // Tự động cập nhật: Món nào có chữ 'seafood' thì điền ID của danh mục đó vào
        \App\Models\Menu::where('category', 'seafood')
            ->update(['category_id' => $category->id]);
    }
}
}
