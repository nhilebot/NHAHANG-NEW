<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bảng NGUỒN CHÍNH của món trong một lượt phục vụ (đặt bàn).
 * Mỗi LẦN GỌI MÓN là một dòng riêng và KHÔNG bao giờ bị xóa khi sửa đơn:
 * giảm/hủy món chỉ tăng cancelled_quantity (kèm lý do), để lịch sử luôn đầy đủ.
 *
 * Migration này CHỈ THÊM bảng mới. Không sửa, không xóa bảng/cột/dữ liệu cũ.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservation_items', function (Blueprint $t) {
            $t->id();

            // Thuộc lượt đặt bàn nào (khớp cách order_items.reservation_id đang cascade)
            $t->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();

            // Món có thể bị xóa khỏi menu sau này -> giữ lại tên/giá đã chụp (snapshot)
            $t->foreignId('menu_id')->nullable()->constrained('menus')->nullOnDelete();
            $t->string('menu_name');
            $t->decimal('unit_price', 15, 2);

            $t->unsignedInteger('quantity');                       // số phần gọi ở lần này (> 0, kiểm tra ở service)
            $t->unsignedInteger('cancelled_quantity')->default(0); // số phần đã hủy/giảm (<= quantity)

            $t->unsignedInteger('batch')->default(1);              // lượt gọi thứ mấy trong đơn
            $t->string('source', 30)->default('legacy');           // customer_preorder | staff_walkin | staff_extra | legacy
            $t->string('status', 20)->default('pending');          // pending | cooking | done | served

            $t->timestamp('sent_at')->nullable();      // gửi bếp
            $t->timestamp('cooking_at')->nullable();   // bếp bắt đầu nấu
            $t->timestamp('done_at')->nullable();      // bếp nấu xong
            $t->timestamp('served_at')->nullable();    // đã bưng ra

            $t->timestamp('cancelled_at')->nullable();
            $t->string('cancel_reason', 255)->nullable();
            $t->string('cancel_stage', 20)->nullable(); // trạng thái của dòng lúc bị hủy (pending/cooking/done)
            $t->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();

            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            // Chống xử lý trùng khi bấm lại/tải lại. KHÔNG dùng id dòng làm khóa.
            // NULL được phép nhiều lần; khi có giá trị thì phải duy nhất.
            $t->string('idempotency_key', 64)->nullable()->unique();

            $t->timestamps();

            $t->index(['reservation_id', 'status']);
            $t->index(['reservation_id', 'menu_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_items');
    }
};