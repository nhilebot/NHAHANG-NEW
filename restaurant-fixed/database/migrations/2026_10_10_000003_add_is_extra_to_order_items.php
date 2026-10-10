<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // true = món khách gọi thêm tại bàn (sau khi đã đặt trước / đặt cọc)
            $table->boolean('is_extra')->default(false)->after('chef_status');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('is_extra');
        });
    }
};
