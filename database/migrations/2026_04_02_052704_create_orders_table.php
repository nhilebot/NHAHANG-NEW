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
        Schema::create('orders', function (Blueprint $table) {
    $table->id();

    $table->foreignId('user_id')->constrained()->onDelete('cascade'); // 🔥 THÊM

    $table->string('table_number')->nullable(); // ✅ giữ 1 dòng thôi
    $table->integer('total_price')->default(0);

    $table->string('status')->default('pending'); 
    // pending | serving | ready | paid

    $table->string('payment_method')->nullable();

    $table->string('name')->nullable();   // 🔥 THÊM
    $table->string('phone')->nullable();  // 🔥 THÊM

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
