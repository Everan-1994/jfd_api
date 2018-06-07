<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('article_id')->comment('文章id');
            $table->unsignedInteger('share_id')->comment('分享者id');
            $table->string('name')->comment('用户名称');
            $table->string('phone')->comment('用户联系方式');
            $table->string('home_type')->comment('户型');
            $table->tinyInteger('status')->default(0)->comment('是否已回访');
            $table->string('remake')->nullable()->comment('标记备注');
            $table->string('ip')->default('0.0.0.0')->comment('ip地址');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
