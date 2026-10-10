<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentFieldsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('orders', function (Blueprint $table) {
        if (!Schema::hasColumn('orders', 'payment_method')) {
            $table->string('payment_method')->nullable()->after('status');
        }
        
        // Cột name bị báo lỗi Duplicate nên ta dùng if kiểm tra
        if (!Schema::hasColumn('orders', 'name')) {
            $table->string('name')->nullable()->after('user_id');
        }

        if (!Schema::hasColumn('orders', 'phone')) {
            $table->string('phone')->nullable()->after('name');
        }

        if (!Schema::hasColumn('orders', 'notes')) {
            $table->text('notes')->nullable()->after('payment_method');
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Xóa các cột nếu rollback migration
            $table->dropColumn(['payment_method', 'name', 'phone', 'notes']);
        });
    }
}