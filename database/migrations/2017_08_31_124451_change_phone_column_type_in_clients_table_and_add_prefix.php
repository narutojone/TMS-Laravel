<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePhoneColumnTypeInClientsTableAndAddPrefix extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Make phone column nullable (need for  changing column type from text to integer)
        Schema::table('clients', function (Blueprint $table) {
            $table->string('phone')->nullable()->change();
        });

        // Update all empty phones to NULL
        DB::statement('UPDATE clients SET phone = NULL where phone  = ""');

        // Change phone column type to unsignedBigInteger
        Schema::table('clients', function (Blueprint $table) {
            $table->unsignedBigInteger('phone')->change();
        });

        // add 47 prefix for all Norway phones
        DB::statement('update clients set phone = CONCAT("47", phone) where LENGTH(phone) = 8;');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
