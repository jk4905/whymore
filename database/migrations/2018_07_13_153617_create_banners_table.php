<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->comment('名称');
            $table->string('image')->comment('图片');
            $table->string('redirect_url')->comment('跳转链接');
            $table->integer('can_redirect')->default(1)->comment('是否可跳转，1-是，2-否');
            $table->integer('type')->default(1)->comment('类型，1-首页');
            $table->integer('sort')->default(0)->comment('排序');
            $table->integer('status')->default(1)->comment('状态，1-上线，2-下线');
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
        Schema::dropIfExists('banners');
    }
}
