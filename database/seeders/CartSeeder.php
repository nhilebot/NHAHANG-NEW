<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CartSeeder extends Seeder
{
    public function run()
    {
        DB::table('carts')->insert([
            [
                'user_id' => \App\Models\User::where('email', 'leyennhi22122006@gmail.com')->value('id'),
                'menu_id' => 1,
                'quantity' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => \App\Models\User::where('email', 'leyennhi22122006@gmail.com')->value('id'),
                'menu_id' => 5,
                'quantity' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // [
            //     'user_id' => \App\Models\User::where('email', 'leyennhi22122006@gmail.com')->value('id'),
            //     'menu_id' => 10,
            //     'quantity' => 3,
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now(),
            // ],
        ]);
    }
}