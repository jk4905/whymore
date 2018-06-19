<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('school_id')->default(0)->comment('学校id');
            $table->string('name', '64')->comment('分类名称');
            $table->integer('status')->default(1)->comment('状态，1-上架，2-下架');
            $table->integer('sort')->default(0)->comment('排序');
            $table->integer('pid')->default(0)->comment('父级分类');
            $table->unique(['school_id', 'name'], 'uni_school_name');
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
        Schema::dropIfExists('categories');
    }
}
