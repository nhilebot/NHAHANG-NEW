<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // ✅ QUAN TRỌNG

class OrderItemSeeder extends Seeder
{
    public function run()
    {
        DB::table('order_items')->insert([
            [
                'order_id' => 1,
                'menu_id' => 1, 
                'product_name' => 'Cua Hấp',
                'quantity' => 1,
                'price' => 250000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_id' => 1,
                 'menu_id' => 2,
                'product_name' => 'Tôm Càng Hấp',
                'quantity' => 1,
                'price' => 220000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}