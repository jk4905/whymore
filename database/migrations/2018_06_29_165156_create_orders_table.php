<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_id')->comment('订单号');
            $table->unsignedBigInteger('user_id')->comment('用户ID');
            $table->decimal('total_amount', '15', '4')->comment('总金额');
            $table->decimal('discount', '15', '4')->comment('折扣金额');
            $table->decimal('freight', '15', '4')->comment('运费');
            $table->decimal('real_amount', '15', '4')->comment('实际支付金额');
            $table->integer('payment_type')->comment('支付类型，1-支付宝，2-微信');
            $table->string('payment_no')->default(0)->comment('支付单号');
            $table->dateTime('paid_at')->nullable()->comment('支付时间');
            $table->integer('coupon_id')->default(0)->comment('优惠券ID');
            $table->string('name')->comment('联系人');
            $table->string('mobile')->comment('联系电话');
            $table->string('address')->comment('地址');
            $table->string('remark')->comment('备注');
            $table->string('ship_name')->default('')->comment('快递名称');
            $table->string('ship_no')->default(0)->comment('快递单号');
            $table->integer('shipping_type')->comment('配送方式，1-快递，2-送货上门');
            $table->dateTime('shipped_at')->nullable()->comment('发货时间');
            $table->integer('status')->default(1)->comment('状态，1-未付款，2-已付款，3-未发货，4-已发货，5-交易完成，6-交易关闭');
            $table->unique('order_id', 'uni_order_id')->comment('订单号唯一键');
            $table->index('user_id', 'idx_user_id')->comment('用户普通键');
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
        Schema::dropIfExists('orders');
    }
}
