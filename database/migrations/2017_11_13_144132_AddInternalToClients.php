<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInternalToClients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Tag for internal project on clients
        Schema::table('clients', function (Blueprint $table) {
            $table->unsignedTinyInteger('internal')->after('active')->default(0);
        });

        // Internal project ID for user
        Schema::table('users', function (Blueprint $table) {
            $table->integer('client_id')->after('pf_id')->unsigned()->nullable();;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Schema::table('clients', function (Blueprint $table) {
//            $table->dropColumn('internal');
//        });

//        Schema::table('users', function (Blueprint $table) {
//            $table->dropColumn('client_id');
//        });
    }
}