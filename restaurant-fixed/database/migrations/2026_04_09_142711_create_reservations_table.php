<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::create('reservations', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->date('reservation_date')->nullable();
        $table->time('reservation_time')->nullable();
        $table->integer('table_id')->nullable();
        $table->string('full_name')->nullable();
        $table->string('phone')->nullable();
        $table->text('notes')->nullable();
        $table->json('cart_data')->nullable();
        $table->json('chef_statuses')->nullable(); // 🔥 THÊM
        
        // Tách biệt trạng thái đơn và thanh toán cho mục đích Data Analytics
        $table->string('reservation_status')->default('pending'); 
        $table->string('payment_status')->default('unpaid');
        $table->string('payment_method')->nullable();
        
        // Cột phục vụ logic dọn bàn
        $table->timestamp('cleanup_started_at')->nullable();
        
        $table->timestamps(); // Chỉ giữ đúng 1 dòng này
    });
}

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};