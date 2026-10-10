<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /** ID cố định, khớp App\Models\Role (không phụ thuộc thứ tự insert). */
    public function run(): void
    {
        $roles = [
            Role::ADMIN    => ['admin',    'Quản trị viên'],
            Role::STAFF    => ['staff',    'Nhân viên phục vụ'],
            Role::CHEF     => ['chef',     'Đầu bếp'],
            Role::CUSTOMER => ['customer', 'Khách hàng'],
        ];

        foreach ($roles as $id => [$name, $display]) {
            DB::table('roles')->updateOrInsert(
                ['id' => $id],
                ['name' => $name, 'display_name' => $display, 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}
