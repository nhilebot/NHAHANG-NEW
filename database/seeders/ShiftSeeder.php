<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seed nhân viên mẫu + lịch ca tuần (cột users.shifts, JSON).
 * Khung giờ: Sáng 06–14, Chiều 14–22, Full 06–22 (xem config/shifts.php).
 * Chạy: php artisan db:seed --class=ShiftSeeder
 */
class ShiftSeeder extends Seeder
{
    /** Thứ tự các ngày trong tuần, khớp key dùng trong code. */
    private const DAYS = ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'];

    public function run(): void
    {
        $full = array_fill_keys(self::DAYS, 'full');

        // [role_id, tên, email, sđt, lịch ca]
        $employees = [
            // Tài khoản test có sẵn: trực Full cả tuần để đăng nhập lúc nào (6–22h) cũng được
            [Role::STAFF, 'Staff', 'staff@gmail.com', '0900000002', $full],
            [Role::CHEF,  'Chef',  'chef@gmail.com',  '0900000003', $full],

            // Phục vụ
            [Role::STAFF, 'Nguyễn Minh Anh', 'staff.sang@example.com',  '0900000011',
                $this->week('morning', 'morning', 'morning', 'morning', 'morning', 'full', 'off')],
            [Role::STAFF, 'Trần Quốc Bảo',   'staff.chieu@example.com', '0900000012',
                $this->week('afternoon', 'afternoon', 'afternoon', 'afternoon', 'afternoon', 'full', 'off')],
            [Role::STAFF, 'Lê Thu Hà',       'staff.xoay@example.com',  '0900000013',
                $this->week('off', 'afternoon', 'morning', 'afternoon', 'morning', 'afternoon', 'morning')],

            // Đầu bếp
            [Role::CHEF, 'Phạm Văn Dũng',  'chef.sang@example.com',  '0900000021',
                $this->week('morning', 'morning', 'morning', 'morning', 'morning', 'morning', 'off')],
            [Role::CHEF, 'Võ Thị Lan',     'chef.chieu@example.com', '0900000022',
                $this->week('afternoon', 'afternoon', 'afternoon', 'afternoon', 'afternoon', 'afternoon', 'off')],
            [Role::CHEF, 'Đặng Hoàng Nam', 'chef.xoay@example.com',  '0900000023',
                $this->week('off', 'morning', 'afternoon', 'morning', 'afternoon', 'full', 'full')],
        ];

        foreach ($employees as [$roleId, $name, $email, $phone, $shifts]) {
            $user = User::firstOrNew(['email' => $email]);

            // Chỉ đặt mật khẩu cho user mới để không ghi đè mật khẩu đã đổi
            if (! $user->exists) {
                $user->password = Hash::make('123456');
            }

            // role_id không nằm trong $fillable → dùng forceFill (seeder tin cậy)
            $user->forceFill([
                'name'    => $name,
                'phone'   => $phone,
                'role_id' => $roleId,
                'shifts'  => $shifts,
            ])->save();
        }
    }

    /** Ghép 7 ca theo thứ tự T2 → CN thành mảng ['T2' => ..., ..., 'CN' => ...]. */
    private function week(string ...$shifts): array
    {
        return array_combine(self::DAYS, $shifts);
    }
}
