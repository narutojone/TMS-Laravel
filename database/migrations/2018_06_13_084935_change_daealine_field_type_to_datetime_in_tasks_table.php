<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDaealineFieldTypeToDatetimeInTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dateTime('deadline')->change();
        });

        \DB::statement("UPDATE tasks SET deadline = DATE_ADD(deadline, INTERVAL '23:59:00' HOUR_SECOND)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('tasks', function (Blueprint $table) {
        //     $table->date('deadline')->change();
        // });
    }
}
