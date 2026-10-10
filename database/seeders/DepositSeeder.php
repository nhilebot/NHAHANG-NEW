<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DepositSeeder  extends Seeder
{
    public function run()
    {
        DB::table('reservations')->insert([
            [
                'user_id' => \App\Models\User::where('email', 'leyennhi22122006@gmail.com')->value('id'),
                'reservation_date' => '2026-04-20',
                'reservation_time' => '18:00:00',
                'table_id' => 1,
                'full_name' => 'Nguyen Van A',
                'phone' => '0123456789',
                'notes' => 'Bàn gần cửa sổ',
                'cart_data' => json_encode([
                    ['name' => 'Cua Hấp', 'quantity' => 1],
                    ['name' => 'Salad trái cây', 'quantity' => 2],
                ]),
                'status' => 'pending',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => \App\Models\User::where('email', 'leyennhi22122006@gmail.com')->value('id'),
                'reservation_date' => '2026-04-21',
                'reservation_time' => '19:00:00',
                'table_id' => 2,
                'full_name' => 'Nguyen Van A',
                'phone' => '0123456789',
                'notes' => 'Sinh nhật',
                'cart_data' => json_encode([
                    ['name' => 'Tôm Càng Xốt Me', 'quantity' => 1],
                    ['name' => 'Bánh kem', 'quantity' => 1],
                ]),
                'status' => 'confirmed',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => \App\Models\User::where('email', 'leyennhi22122006@gmail.com')->value('id'),
                'reservation_date' => '2026-04-22',
                'reservation_time' => '17:30:00',
                'table_id' => 3,
                'full_name' => 'Nguyen Van A',
                'phone' => '0123456789',
                'notes' => null,
                'cart_data' => null,
                'status' => 'pending',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}