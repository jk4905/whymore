<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeSuffixInRobotConf extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('robot_configurations', function (Blueprint $table) {
            $table->text('suffix')->nullable()->comment('后缀')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('robot_configurations', function (Blueprint $table) {
            $table->string('suffix')->nullable()->comment('后缀');
        });
    }
}
