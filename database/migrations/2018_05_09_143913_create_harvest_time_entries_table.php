<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHarvestTimeEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('harvest_time_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('external_id');
            $table->unsignedInteger('github_issue')->nullable()->default(null);
            $table->float('tracked_time', 4, 2)->unsigned();
            $table->boolean('ignored')->default(0);
            $table->text('notes')->nullable()->default(null);
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
        Schema::dropIfExists('harvest_time_entries');
    }
}
