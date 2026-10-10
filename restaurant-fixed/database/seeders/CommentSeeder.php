<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    public function run()
    {
        // Lấy menu đầu tiên
        $menu = Menu::first();
        
        // Lấy user đầu tiên (hoặc tạo mới)
        $user = User::where('email', 'customer@gmail.com')->first();
        
        if($menu && $user) {
            Comment::create([
                'menu_id' => $menu->id,
                'user_id' => $user->id,
                'content' => 'Món ăn rất ngon! Tôi rất thích, sẽ quay lại ủng hộ.',
                'rating' => 5,
                'status' => 1,
            ]);
            
            Comment::create([
                'menu_id' => $menu->id,
                'user_id' => $user->id,
                'content' => 'Giá cả hợp lý, chất lượng tốt.',
                'rating' => 4,
                'status' => 1,
            ]);
        }
    }
}