<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->comment('用户ID');
            $table->string('name')->comment('联系人');
            $table->string('mobile', 20)->comment('联系人电话');
            $table->bigInteger('province_id')->comment('省份id');
            $table->string('province_name', '255')->comment('省份名');
            $table->bigInteger('city_id')->comment('城市id');
            $table->string('city_name', '255')->comment('城市名');
            $table->bigInteger('area_id')->comment('地区id');
            $table->string('area_name', '255')->comment('地区名');
            $table->string('detailed_address', '255')->comment('详细地址');
            $table->integer('is_default')->default(2)->comment('默认，1-是，2-不是');
            $table->index('user_id', 'idx_user_id')->comment('普通键');
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
        Schema::dropIfExists('addresses');
    }
}
