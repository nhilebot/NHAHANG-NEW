<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Giai đoạn 1: chỉ TẠO BẢNG MỚI `promotions`.
     * Không đụng tới bảng reservations / orders / order_items
     * nên không ảnh hưởng luồng đặt bàn, cọc 20% và hóa đơn hiện tại.
     */
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();          // Mã khách nhập, VD: AURORA10
            $table->string('name');                         // Tên chương trình
            $table->text('description')->nullable();

            $table->string('discount_type', 10);            // percent | fixed
            $table->unsignedInteger('discount_value');      // % (1-100) hoặc số tiền VNĐ
            $table->unsignedInteger('min_order_amount')->default(0);    // Tiền món tối thiểu

            $table->unsignedInteger('usage_limit')->nullable();         // null = không giới hạn
            $table->unsignedInteger('used_count')->default(0);

            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
