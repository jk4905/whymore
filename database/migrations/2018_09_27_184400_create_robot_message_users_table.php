<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRobotMessageUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('robot_message_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('qq')->default(0)->comment('qq号');
            $table->string('user_name')->default('')->comment('昵称');
            $table->string('avatar')->default('')->comment('头像');
            $table->unsignedTinyInteger('sex')->default(0)->comment('性别，1-男，0-女');
            $table->unsignedTinyInteger('age')->default(0)->comment('年龄');
//            $table->date('birthday')->default('2000-01-01')->comment('生日');
            $table->unique('qq');
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
        Schema::dropIfExists('robot_message_users');
    }
}
