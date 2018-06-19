<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('cate_id')->comment('类型');
            $table->string('name', 100)->comment('商品名');
            $table->string('description', 256)->comment('描述');
            $table->string('image', 256)->comment('图片');
            $table->decimal('sale_price', 15, 4)->default(0)->comment('售价');
            $table->integer('sales')->default(0)->comment('销售量');
            $table->string('keyword', 256)->comment('关键词');
            $table->boolean('status')->default(1)->comment('状态，1-上架，2-下架');
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
        Schema::dropIfExists('goods');
    }
}
