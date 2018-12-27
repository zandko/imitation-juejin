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
            $table->increments('id')->comment('主键');
            $table->string('title')->comment('标题');
            $table->text('content')->comment('内容');
            $table->unsignedInteger('user_id')->comment('发布用户ID');
            $table->string('user_name')->comment('发布用户');
            $table->unsignedInteger('category_id')->comment('所属分类');
            $table->unsignedInteger('lable_id')->nullable()->comment('所属标签');
            $table->string('image')->nullable()->comment('封面图');
            $table->tinyInteger('state')->comment('状态');
            $table->unsignedInteger('order')->default(0)->comment('排序');
            $table->unsignedInteger('read_count')->default(0)->comment('阅读次数');
            $table->unsignedInteger('like_count')->default(0)->comment('点赞次数');
            $table->unsignedInteger('reply_count')->default(0)->comment('回复次数');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('lable_id')->references('id')->on('lables')->onDelete('set null');
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
