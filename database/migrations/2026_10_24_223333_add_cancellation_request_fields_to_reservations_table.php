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
    Schema::table('reservations', function (Blueprint $table) {
        $table->string('cancellation_status')->nullable();
        $table->text('cancellation_reason')->nullable();
        $table->timestamp('cancellation_requested_at')->nullable();
        $table->timestamp('cancellation_reviewed_at')->nullable();
        $table->unsignedBigInteger('cancellation_reviewed_by')->nullable();
    });
}

public function down(): void
{
    Schema::table('reservations', function (Blueprint $table) {
        $table->dropColumn([
            'cancellation_status',
            'cancellation_reason',
            'cancellation_requested_at',
            'cancellation_reviewed_at',
            'cancellation_reviewed_by',
        ]);
    });
}
};
