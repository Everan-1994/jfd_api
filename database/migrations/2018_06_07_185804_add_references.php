<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReferences extends Migration
{
    public function up()
    {
        Schema::table('images', function (Blueprint $table) {
            // 当 article_id 对应的 article 表数据被删除时，删除此条
            $table->foreign('article_id')->references('id')->on('articles')->onDelete('cascade');
        });

        Schema::table('messages', function (Blueprint $table) {
            // 当 article_id 对应的 users 表数据被删除时，删除此条数据
            $table->foreign('article_id')->references('id')->on('articles')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('images', function (Blueprint $table) {
            // 移除外键约束
            $table->dropForeign(['article_id']);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['article_id']);
        });

    }
}
