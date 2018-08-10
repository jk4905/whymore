<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedbackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 2048)->comment('名称');
            $table->string('image', 2048)->default('')->comment('图片');
            $table->bigInteger('user_id')->default(0)->comment('用户 ID');
            $table->tinyInteger('is_deleted')->default(0)->comment('是否删除，0-否，1-是');
            $table->tinyInteger('status')->default(1)->comment('状态，1-未解决，2-解决');
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
        Schema::dropIfExists('feedback');
    }
}
