<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterClientsTableRenameIndustryToSystmeId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->renameColumn('industry', 'system_id');
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->unsignedInteger('system_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->renameColumn('system_id', 'industry');
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->string('industry')->change();
        });
    }
}
