<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsTable extends Migration
{
    public function up()
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();          // đường dẫn đẹp: /news/ten-bai-viet
            $table->string('category')->default('Tin nhà hàng');
            $table->text('excerpt')->nullable();       // mô tả ngắn hiện ở danh sách
            $table->longText('content');
            $table->string('image')->nullable();       // vd: images/news/khai-truong.jpg (nằm trong public/)
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('news');
    }
}
