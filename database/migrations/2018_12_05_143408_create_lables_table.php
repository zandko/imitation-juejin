<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lables', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('名称');
            $table->text('description')->nullable()->comment('描述');
            $table->string('image')->comment('图标');
            $table->unsignedInteger('post_count')->default(0)->comment('文章总数');
            $table->unsignedInteger('follow_count')->default(0)->comment('关注次数');
            $table->unsignedInteger('order')->default(0)->comment('排序');
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
        Schema::dropIfExists('lables');
    }
}
