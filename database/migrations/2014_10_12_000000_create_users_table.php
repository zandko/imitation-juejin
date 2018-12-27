<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id')->comment('主键');
            $table->string('name')->comment('用户名');
            $table->string('password')->nullable()->comment('密码');

            $table->string('email')->unique()->nullable()->comment('邮箱');
            $table->string('phone')->unique()->nullable()->comment('手机号');


            $table->string('provider')->nullable()->comment('服务提供方');
            $table->string('provider_id')->nullable()->comment('第三方那里获取的用户唯一ID');

            $table->unsignedInteger('notification_count')->default(0)->comment('未读消息');

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
        Schema::dropIfExists('users');
    }
}
