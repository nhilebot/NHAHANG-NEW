<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_issues', function (Blueprint $t) {
            $t->id();
            $t->string('code', 40)->unique();
            $t->string('reason', 20);                  // kitchen | waste | spoiled | discard | other
            $t->date('issued_at');
            $t->decimal('total_amount', 16, 2)->default(0);   // giá trị theo giá tham khảo lúc xuất
            $t->string('note', 255)->nullable();
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
        });

        Schema::create('stock_issue_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('stock_issue_id')->constrained('stock_issues')->cascadeOnDelete();
            $t->foreignId('ingredient_id')->constrained('ingredients')->restrictOnDelete();
            $t->decimal('quantity', 14, 3);
            $t->decimal('unit_cost', 14, 2)->default(0);
            $t->decimal('line_total', 16, 2)->default(0);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_issue_items');
        Schema::dropIfExists('stock_issues');
    }
};
