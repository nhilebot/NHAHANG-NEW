<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Công thức định lượng món ăn: mỗi món (menus) có tối đa MỘT công thức,
 * mỗi công thức gồm nhiều nguyên liệu với định lượng CHO MỘT PHẦN ĂN.
 *
 * Migration này CHỈ THÊM bảng mới. Không sửa, không xóa bảng/cột/dữ liệu cũ.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $t) {
            $t->id();

            // Mỗi món một công thức (unique) => không thể trùng công thức cho cùng một món
            $t->foreignId('menu_id')->unique()->constrained('menus')->cascadeOnDelete();

            // true = số liệu MINH HỌA do hệ thống đề xuất, cần nhà hàng xác nhận (không phải công thức chuẩn)
            $t->boolean('is_sample')->default(false);
            $t->string('note', 255)->nullable();

            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
        });

        Schema::create('recipe_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('recipe_id')->constrained('recipes')->cascadeOnDelete();

            // Không cho xóa nguyên liệu đang nằm trong công thức
            $t->foreignId('ingredient_id')->constrained('ingredients')->restrictOnDelete();

            // Định lượng cho MỘT phần ăn, theo đơn vị `unit` bên dưới.
            // quantity > 0 và unit tương thích với đơn vị kho: kiểm tra ở tầng ứng dụng (UnitConverter).
            $t->decimal('quantity', 14, 3);
            $t->string('unit', 30);

            $t->timestamps();

            // Một nguyên liệu chỉ xuất hiện MỘT lần trong cùng một công thức
            $t->unique(['recipe_id', 'ingredient_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipe_items');
        Schema::dropIfExists('recipes');
    }
};
