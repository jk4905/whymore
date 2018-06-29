<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_coupons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->comment('用户id');
            $table->bigInteger('coupon_id')->comment('优惠券id');
            $table->bigInteger('begin_at')->comment('优惠开始时间');
            $table->bigInteger('end_at')->comment('优惠结束时间');
            $table->integer('status')->default(1)->comment('优惠券状态，1-待使用、2-占用中、3-已使用、4-已失效/已过期');
            $table->unique(['user_id','coupon_id'],'uni_user_coupon')->comment('用户-优惠券唯一键');
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
        Schema::dropIfExists('user_coupons');
    }
}
