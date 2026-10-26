<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Giai đoạn 2: chỉ THÊM 3 cột vào reservations, không đổi/xóa cột nào.
     *  - promotion_id / promotion_code : mã đang gắn với đơn (chưa trừ tiền)
     *  - discount_amount               : số tiền giảm ĐÃ CHỐT lúc thanh toán (mặc định 0)
     * total_price vẫn là tiền món gốc; deposit_amount vẫn tính trên tiền món gốc.
     */
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->foreignId('promotion_id')->nullable()
                  ->constrained('promotions')->nullOnDelete();
            $table->string('promotion_code', 30)->nullable();
            $table->unsignedInteger('discount_amount')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('promotion_id');
            $table->dropColumn(['promotion_code', 'discount_amount']);
        });
    }
};
