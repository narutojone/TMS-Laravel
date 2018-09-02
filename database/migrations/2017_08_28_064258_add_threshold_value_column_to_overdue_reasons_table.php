<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddThresholdValueColumnToOverdueReasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('overdue_reasons', function (Blueprint $table) {
            $table->integer('threshold_value')->after('active')->nullable();
            $table->tinyInteger('is_visible_in_report')->unsigned()->after('active')->nullable()->default(1);
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
