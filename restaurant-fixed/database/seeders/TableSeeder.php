<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
      $tables = [

    ['name'=>'Bàn 01','capacity'=>12,'status'=>'empty','cleanup_started_at'=>null],

    ['name'=>'Bàn 02','capacity'=>12,'status'=>'empty','cleanup_started_at'=>null],

    ['name'=>'Bàn 03','capacity'=>12,'status'=>'empty','cleanup_started_at'=>null],

    ['name'=>'Bàn 04','capacity'=>6,'status'=>'empty','cleanup_started_at'=>null],

    ['name'=>'Bàn 05','capacity'=>8,'status'=>'empty','cleanup_started_at'=>null],

    ['name'=>'Bàn 06','capacity'=>10,'status'=>'empty','cleanup_started_at'=>null],

    ['name'=>'Bàn 07','capacity'=>4,'status'=>'empty','cleanup_started_at'=>null],

    ['name'=>'Bàn 08','capacity'=>4,'status'=>'empty','cleanup_started_at'=>null],

    ['name'=>'Bàn 09','capacity'=>2,'status'=>'empty','cleanup_started_at'=>null],

    ['name'=>'Bàn 10','capacity'=>2,'status'=>'empty','cleanup_started_at'=>null],

    ['name'=>'Bàn VIP 01','capacity'=>15,'status'=>'empty','cleanup_started_at'=>null],

    ['name'=>'Bàn VIP 02','capacity'=>20,'status'=>'empty','cleanup_started_at'=>null],

    ['name'=>'Bàn VIP 03','capacity'=>25,'status'=>'empty','cleanup_started_at'=>null],

    ['name'=>'Bàn Ngoài Trời 01','capacity'=>5,'status'=>'empty','cleanup_started_at'=>null],
    
    ['name'=>'Bàn Ngoài Trời 02','capacity'=>4,'status'=>'empty','cleanup_started_at'=>null],

    ['name'=>'Bàn Ngoài Trời 03','capacity'=>2,'status'=>'empty','cleanup_started_at'=>null],

];

        foreach ($tables as $table) {
            // Sử dụng updateOrInsert để nếu có tên bàn rồi thì chỉ cập nhật, chưa có thì mới thêm
            DB::table('tables')->updateOrInsert(
                ['name' => $table['name']], 
                $table
            );
        }
    }
}