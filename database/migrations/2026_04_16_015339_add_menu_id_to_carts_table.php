<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Kiểm tra nếu cột 'menu_id' chưa tồn tại thì mới thêm vào
        if (!Schema::hasColumn('carts', 'menu_id')) {
            Schema::table('carts', function (Blueprint $table) {
                // Sử dụng foreignId giúp code ngắn gọn hơn
                $table->foreignId('menu_id')
                      ->after('user_id')
                      ->constrained('menus') // Tự động hiểu là nối với id của bảng menus
                      ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            // Kiểm tra nếu có cột thì mới xóa khi rollback
            if (Schema::hasColumn('carts', 'menu_id')) {
                // Lưu ý: Phải xóa khóa ngoại trước khi xóa cột
                // $table->dropForeign(['menu_id']);
                $table->dropColumn('menu_id');
            }
        });
    }
};