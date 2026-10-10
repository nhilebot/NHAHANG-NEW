<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Sửa integer() thành string() và cho phép trống (nullable)
            $table->string('table_number')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Hàm down cũng sửa về dạng string để không bị lỗi khi rollback
            $table->string('table_number')->nullable(false)->change();
        });
    }
};