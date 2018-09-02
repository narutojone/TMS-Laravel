<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSpentDateColumnToHarvestTimeEntries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('harvest_time_entries', function (Blueprint $table) {
            $table->date('spent_date')->after('tracked_time')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('harvest_time_entries', function (Blueprint $table) {
            $table->dropColumn('spent_date');
        });
    }
}
