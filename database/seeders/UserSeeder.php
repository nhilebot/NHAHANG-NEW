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
            [Role::ADMIN,    'Admin',      'admin@gmail.com',                 '0900000001', 'Admin@2026'],
            [Role::STAFF,    'Staff',      'staff@gmail.com',                 '0900000002', 'Staff@2026'],
            [Role::CHEF,     'Chef',       'chef@gmail.com',                  '0900000003', 'Chef@2026'],
            [Role::CUSTOMER, 'Yến Nhi',    'leyennhi22122006@gmail.com',      '0900000004', 'Nhi@2026'],
        ];

        foreach ($users as [$roleId, $name, $email, $phone, $password]) {
            $user = User::firstOrNew(['email' => $email]);

            // Chỉ cấp mật khẩu demo riêng khi tạo mới; không ghi đè mật khẩu đã có.
            if (! $user->exists) {
                $user->password = Hash::make($password);
            }

            // role_id không nằm trong $fillable nên dùng forceFill (chỉ trong seeder tin cậy)
            $user->forceFill([
                'name'     => $name,
                'phone'    => $phone,
                'role_id'  => $roleId,
            ])->save();
        }
    }
}
