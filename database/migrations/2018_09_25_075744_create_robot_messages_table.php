<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRobotMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('robot_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('from')->default(0)->comment('用户QQ');
            $table->unsignedBigInteger('to')->comment('机器人QQ');
            $table->dateTime('send_time')->comment('发送时间');
            $table->text('content')->comment('消息内容');
            $table->text('images')->comment('消息内容中的图片链接');
            $table->unsignedTinyInteger('type')->comment('消息类型，1-用户发送，2-帖子发送');
//            $table->unsignedInteger('school_id')->default(0)->comment('学校ID');
//            $table->unsignedBigInteger('group_num')->default(0)->comment('群号');
            $table->tinyInteger('status')->default(1)->comment('状态：1-有效，2-删除');
            $table->index('from', 'idx_from');
            $table->index('to', 'idx_to');
            $table->index('send_time', 'idx_send_time');
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
        Schema::dropIfExists('robot_messages');
    }
}
