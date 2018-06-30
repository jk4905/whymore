<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_id')->comment('订单号');
            $table->bigInteger('goods_id')->comment('商品ID');
            $table->string('goods_name')->comment('商品名');
            $table->string('description')->comment('描述');
            $table->string('image')->comment('图片');
            $table->decimal('price', '15', '4')->comment('商品单价');
            $table->decimal('discount', '15', '4')->comment('折扣价格');
            $table->decimal('item_amount', '15', '4')->comment('实际支付总价，商品单价*数量-折扣价格');
            $table->integer('qty')->comment('数量');
            $table->unique(['order_id', 'goods_id'], 'uni_order_goods')->comment('订单号和商品id唯一键');
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
        Schema::dropIfExists('order_items');
    }
}
