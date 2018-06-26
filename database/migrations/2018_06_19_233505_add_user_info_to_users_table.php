<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserInfoToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('email');
            $table->string('mobile', 20)->after('id')->comment('手机号');
            $table->string('avatar', 255)->after('mobile')->nullable()->comment('头像');
            $table->string('name', 255)->nullable()->after('avatar')->comment('昵称')->change();
            $table->unique('mobile', 'uni_mobile')->comment('手机号唯一键');
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
            $table->string('email', '255')->comment('email');
            $table->dropUnique('uni_mobile');
            $table->dropColumn('avatar');
            $table->dropColumn('mobile');
        });
    }
}
