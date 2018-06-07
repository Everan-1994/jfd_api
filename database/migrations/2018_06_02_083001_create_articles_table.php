<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('subtitle')->comment('副标题');
            $table->text('up_body')->comment('上半区内容');
            $table->text('down_body')->comment('下半区内容');
            $table->integer('views')->default(0)->comment('阅读数');
            $table->integer('true_views')->default(0)->comment('真实阅读数');
            $table->integer('asks')->default(0)->comment('咨询数');
            $table->integer('true_asks')->default(0)->comment('真实咨询数');
            $table->tinyInteger('status')->default(0);
            $table->string('phone')->comment('作者手机号');
            $table->unsignedInteger('author_id')->index()->comment('作者id');
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
        Schema::dropIfExists('articles');
    }
}
