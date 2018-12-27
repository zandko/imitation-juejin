<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_infos', function (Blueprint $table) {
            $table->increments('id')->comment('主键');
            $table->unsignedInteger('user_id')->comment('用户');
            $table->string('avatar')->nullable()->comment('头像');
            $table->text('introduction')->nullable()->comment('个人介绍');
            $table->string('company')->nullable()->comment('公司');
            $table->string('position')->nullable()->comment('职位');
            $table->string('homepage')->nullable()->comment('个人主页');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_infos');
    }
}
