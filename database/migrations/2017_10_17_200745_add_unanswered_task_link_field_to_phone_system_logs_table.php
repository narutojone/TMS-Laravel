<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUnansweredTaskLinkFieldToPhoneSystemLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('phone_system_logs', function (Blueprint $table) {
            $table->unsignedInteger('task_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('phone_system_logs', function (Blueprint $table) {
            $table->dropColumn('task_id');
        });
    }
}
