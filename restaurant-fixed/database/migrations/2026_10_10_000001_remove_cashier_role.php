<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Gỡ vai trò Thu ngân (role_id = 1) cho CSDL cũ.
 * Tài khoản thu ngân được chuyển thành Nhân viên (Staff) vì Staff đã nhận nhiệm vụ thanh toán.
 * Với CSDL mới (migrate:fresh), role này không còn được tạo nên migration này là no-op.
 */
return new class extends Migration
{
    public function up(): void
    {
        $cashierId = DB::table('roles')->where('name', 'cashier')->value('id');

        if ($cashierId) {
            DB::table('users')->where('role_id', $cashierId)->update(['role_id' => 3]); // Staff
            DB::table('roles')->where('id', $cashierId)->delete();
        }
    }

    public function down(): void
    {
        // Không khôi phục vai trò Thu ngân.
    }
};
