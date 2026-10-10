<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kiểm tra tránh trùng lặp nếu bạn đã lỡ tạo cột này trước đó
            if (!Schema::hasColumn('users', 'shifts')) {
                // Thêm cột shifts kiểu JSON, cho phép null
                $table->json('shifts')->nullable()->after('email'); 
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'shifts')) {
                $table->dropColumn('shifts');
            }
        });
    }
};