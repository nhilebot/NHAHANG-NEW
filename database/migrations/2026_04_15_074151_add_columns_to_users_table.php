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
    Schema::table('users', function (Blueprint $table) {
        // Kiểm tra nếu chưa có cột phone thì mới thêm
        if (!Schema::hasColumn('users', 'phone')) {
            $table->string('phone')->nullable()->after('email');
        }
        
        // Kiểm tra nếu chưa có cột address thì mới thêm
        if (!Schema::hasColumn('users', 'address')) {
            $table->string('address')->nullable()->after('phone');
        }

        // Kiểm tra nếu chưa có cột city thì mới thêm
        if (!Schema::hasColumn('users', 'city')) {
            $table->string('city')->nullable()->after('address');
        }
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['phone', 'address', 'city']);
    });
}
};
