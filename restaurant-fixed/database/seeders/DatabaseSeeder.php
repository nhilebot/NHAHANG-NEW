<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RoleSeeder::class,   // phải chạy trước UserSeeder (khoá ngoại role_id)
            UserSeeder::class,
            CategorySeeder::class,
            MenuSeeder::class,
            TableSeeder::class,
            OrderSeeder::class,
            OrderItemSeeder::class,
            ReservationSeeder::class,
            CartSeeder::class,
        ]);
    }
}