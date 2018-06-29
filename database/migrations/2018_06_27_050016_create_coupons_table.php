<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 50)->comment('优惠券名');
            $table->decimal('condition', 15, 4)->comment('单笔消费满多少才能使用');
            $table->decimal('discount', 15, 4)->comment('折扣额度');
            $table->integer('type')->default(1)->comment('类型，1-满减，2-打折');
            $table->string('category_id')->default(0)->comment('商品分类，0-所有，其余多个用逗号隔开');
            $table->integer('expires')->default(1)->comment('有效期');
            $table->string('description')->comment('使用条件描述');
            $table->integer('status')->default(1)->comment('状态，1-开启，2-禁止');
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
        Schema::dropIfExists('coupons');
    }
}
