<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGroupToRobotMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('robot_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('group')->default(0)->comment('群号');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('robot_messages', function (Blueprint $table) {
            $table->dropColumn('group');
        });
    }
}
