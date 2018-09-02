<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZendeskUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zendesk_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('zendesk_id', 20)->nullable();
            $table->string('name', 255)->nullable();
            $table->string('email', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zendesk_users');
    }
}
