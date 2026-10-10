<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('carts', function (Blueprint $table) {
        $table->id();
        // Liên kết với bảng users (người dùng phải đăng nhập)
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        // Liên kết với bảng menus (món ăn)
        $table->unsignedBigInteger('menu_id');
        // Số lượng món ăn
        $table->integer('quantity')->default(1);
        $table->timestamps();
        
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
