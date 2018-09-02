<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFlagsTableRegardingClientSpecificAndNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('flags', function (Blueprint $table) {
            $table->unsignedTinyInteger('client_specific')->after('days')->default(0);
            $table->unsignedTinyInteger('client_removal')->after('client_specific')->default(0);
            $table->text('sms')->after('client_removal')->nullable();
        });

        Schema::table('flag_user', function (Blueprint $table) {
            $table->integer('client_id')->after('user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
