<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ConvertOverdueReasonExpirationDateToDateTime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('task_overdue_reasons', function (Blueprint $table) {
            $table->datetime('expiration_date')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Schema::table('task_overdue_reasons', function (Blueprint $table) {
//            $table->date('expiration_date')->change();
//        });
    }
}
