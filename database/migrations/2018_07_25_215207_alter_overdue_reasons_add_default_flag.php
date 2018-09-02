<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterOverdueReasonsAddDefaultFlag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('overdue_reasons', function (Blueprint $table) {
            $table->unsignedTinyInteger('default')->after('visible')->default(1);
        });

        Schema::create('template_overdue_reasons', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('template_id');
            $table->unsignedInteger('overdue_reason_id');
            $table->enum('trigger_type', ['none', 'consecutive', 'total']);
            $table->unsignedSmallInteger('trigger_counter')->nullable();
            $table->enum('action', ['hide_reason', 'pause_client', 'deactivate_client', 'remove_employee'])->nullable();
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
        //
    }
}
