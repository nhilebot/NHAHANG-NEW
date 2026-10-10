<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Thêm cột reservation_id (có thể null vì đơn mang về sẽ không có cột này)
            $table->unsignedBigInteger('reservation_id')->nullable()->after('order_id');
            
            // Cho phép order_id được null (trường hợp khách ăn tại bàn thì order_id = null)
            $table->unsignedBigInteger('order_id')->nullable()->change();

            // Thiết lập khóa ngoại (Khi xóa reservation thì tự động xóa món ăn)
            $table->foreign('reservation_id')
                  ->references('id')
                  ->on('reservations')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['reservation_id']);
            $table->dropColumn('reservation_id');
        });
    }
};