<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Số tiền thực thu tại quầy = total_price - tiền cọc đã nhận
            $table->unsignedBigInteger('paid_amount')->nullable()->after('deposit_amount');
            $table->timestamp('paid_at')->nullable()->after('paid_amount');
            $table->foreignId('paid_by')->nullable()->after('paid_at')
                  ->constrained('users')->nullOnDelete(); // nhân viên xác nhận thanh toán
            $table->timestamp('deposit_paid_at')->nullable()->after('paid_by');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['paid_by']);
            $table->dropIndex(['status']);
            $table->dropColumn(['paid_amount', 'paid_at', 'paid_by', 'deposit_paid_at']);
        });
    }
};
