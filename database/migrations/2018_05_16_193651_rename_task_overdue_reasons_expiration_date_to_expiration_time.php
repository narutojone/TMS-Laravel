<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameTaskOverdueReasonsExpirationDateToExpirationTime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('task_overdue_reasons', function (Blueprint $table) {
            $table->renameColumn('expiration_date', 'expired_at');
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
//            $table->renameColumn('expired_at', 'expiration_date');
//        });
    }
}
