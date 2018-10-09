<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQqInfoToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('qq')->default(0)->comment('qq号');
            $table->string('qq_open_id')->default(0)->comment('qq的open_id');
            $table->unsignedTinyInteger('sex')->default(0)->comment('性别，1-男，0-女');
            $table->unsignedTinyInteger('age')->default(0)->comment('年龄');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('qq');
            $table->dropColumn('qq_open_id');
            $table->dropColumn('sex');
            $table->dropColumn('age');
            $table->dropColumn('birthday');
        });
    }
}
