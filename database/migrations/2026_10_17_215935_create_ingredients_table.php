<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 150);
            $table->foreignId('category_id')
                  ->constrained('ingredient_categories')
                  ->restrictOnDelete();
            $table->string('unit', 30);
            // Tồn kho nguyên liệu. KHÔNG liên quan menus.stock (số phần món có thể bán).
            // Chỉ được thay đổi qua phiếu nhập/xuất/kiểm kê (giai đoạn sau).
            $table->decimal('current_stock', 14, 3)->default(0);
            $table->decimal('min_stock', 14, 3)->default(0);
            $table->decimal('reference_price', 14, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('note')->nullable();
            $table->timestamps();

            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};