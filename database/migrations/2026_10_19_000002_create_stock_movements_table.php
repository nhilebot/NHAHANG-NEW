<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Sổ cái kho: mỗi lần tồn kho thay đổi là một dòng, KHÔNG sửa/xóa về sau.
        Schema::create('stock_movements', function (Blueprint $t) {
            $t->id();
            $t->foreignId('ingredient_id')->constrained('ingredients')->restrictOnDelete();
            $t->string('type', 20);                       // in | out | adjust
            $t->decimal('quantity', 14, 3);               // CÓ DẤU: + tăng, - giảm
            $t->decimal('balance_after', 14, 3);          // tồn sau khi áp dụng
            $t->decimal('unit_cost', 14, 2)->nullable();
            $t->nullableMorphs('reference');              // phiếu gốc (phiếu nhập, xuất, kiểm kê...)
            $t->string('note', 255)->nullable();
            $t->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('created_at')->useCurrent();

            $t->index(['ingredient_id', 'created_at']);
            $t->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
