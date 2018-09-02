<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTemplateNotificationsTableAddUserTypeColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('template_notifications', function (Blueprint $table) {
            $table->enum('user_type', ['client', 'employee', 'manager'])->after('type')->default('client');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('template_notifications', function (Blueprint $table) {
            $table->dropColumn('user_type');
        });
    }
}
