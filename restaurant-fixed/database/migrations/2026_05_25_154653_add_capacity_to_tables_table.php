<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tables', function (Blueprint $table) {

            // thêm cột số ghế
            $table->integer('capacity')
                  ->default(4)
                  ->after('name');

        });
    }

    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {

            $table->dropColumn('capacity');

        });
    }
};