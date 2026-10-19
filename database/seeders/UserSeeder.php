<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [Role::ADMIN,    'Admin',        'admin@gmail.com',    '0900000001'],
            [Role::STAFF,    'Staff',        'staff@gmail.com',    '0900000002'],
            [Role::CHEF,     'Chef',         'chef@gmail.com',     '0900000003'],
            [Role::CUSTOMER, 'Yến Nhi',   'leyennhi22122006@gmail.com', '0900000004'],
        ];

        foreach ($users as [$roleId, $name, $email, $phone]) {
            // role_id không nằm trong $fillable nên dùng forceFill (chỉ trong seeder tin cậy)
            User::firstOrNew(['email' => $email])
                ->forceFill([
                    'name'     => $name,
                    'phone'    => $phone,
                    'password' => Hash::make('123456'),
                    'role_id'  => $roleId,
                ])->save();
        }
    }
}
