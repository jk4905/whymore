<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRobotConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('robot_configurations', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('主键');
            $table->string('robot_num')->comment('机器人号');
            $table->unsignedInteger('admin_id')->default(1)->comment('管理员id');

            $table->text('sensitive_words')->nullable()->comment('敏感词');
            $table->string('suffix')->nullable()->comment('后缀');

            $table->text('transmit_group_num')->nullable()->comment('转发群号');
            $table->text('non_friend_msg')->nullable()->comment('非好友回复');
            $table->text('become_friend_msg')->nullable()->comment('成为好友回复');
            $table->text('transmit_success_msg')->nullable()->comment('转发成功回复');

            $table->text('black_list')->nullable()->comment('黑名单');
            $table->string('black_list_msg')->nullable()->comment('黑名单回复');
            $table->unsignedInteger('warning_blacklist_count')->nullable()->default(0)->comment('拉黑警告次数');

            $table->text('key_words')->nullable()->comment('转发关键字');
            $table->text('not_key_words_msg')->nullable()->comment('未有关键字回复');

            $table->unsignedInteger('transmit_length')->default(10)->comment('转发内容长度');
            $table->string('transmit_length_wrong_msg')->default('消息太简短不够10字节，请重新编辑你的消息哟！')->comment('转发内容长度不达标回复');
            $table->string('sensitive_words_msg')->nullable()->comment('包含敏感词回复');

            $table->unsignedInteger('suffix_odds')->default(0)->comment('后缀出现几率');
            $table->unsignedInteger('achieve_level')->default(0)->comment('达标等级');
            $table->string('not_achieve_level_msg')->nullable()->comment('不达标等级回复');

            $table->unsignedInteger('invalid_begin_at')->default(0)->comment('机器人无效开始时间');
            $table->unsignedInteger('invalid_end_at')->default(4)->comment('机器人无效结束时间');
            $table->unsignedInteger('transmit_delay')->default(200)->comment('转发延迟时间（单位：ms）');

            $table->string('group_admin')->nullable()->comment('管理员QQ');
            $table->unsignedInteger('send_count')->default(0)->comment('消息发布限制次数');
            $table->string('overstep_send_count_msg')->nullable()->comment('超出次数回复');

            $table->unsignedInteger('send_score')->default(0)->comment('发布一次所需积分');
            $table->unsignedInteger('recharge_score')->default(10)->comment('充值一元获得积分');
            $table->unsignedInteger('reply_min_score')->default(0)->comment('回复最小积分');
            $table->unsignedInteger('reply_max_score')->default(0)->comment('回复最大积分');
            $table->unsignedInteger('sign_in_min_score')->default(0)->comment('签到最小积分');
            $table->unsignedInteger('sign_in_max_score')->default(0)->comment('签到最大积分');


            $table->unsignedInteger('remove_blank_list_count')->default(3)->comment('解除黑名单所需积分');
            $table->string('remove_blank_list_msg')->default('消耗三积分，已经自动为您解除黑名单，请你注意发布消息的质量哦！')->comment('解除黑名单成功回复');


            // $table->text('menu')->comment('菜单');
            $table->unsignedInteger('status')->default(1)->comment('状态 1-开启，2-禁用');
            $table->string('remark')->nullable()->comment('备注');
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
        Schema::dropIfExists('robot_configurations');
    }
}
