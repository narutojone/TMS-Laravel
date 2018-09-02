<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToTemplateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('template_notifications', function (Blueprint $table) {
            $table->tinyInteger('paid')->default(1)->unsigned()->after('details');
            $table->tinyInteger('completed')->default(0)->unsigned()->after('paid');
            $table->tinyInteger('delivered')->default(0)->unsigned()->after('completed');
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
