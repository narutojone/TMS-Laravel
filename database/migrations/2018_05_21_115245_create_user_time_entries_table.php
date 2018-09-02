<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTimeEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_time_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('external_id')->nullable();
            $table->unsignedInteger('client_id')->nullable();
            $table->unsignedInteger('harvest_client_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('harvest_user_id')->nullable();
            $table->date('spent_date')->nullable();
            $table->decimal('hours', 5, 2)->unsigned()->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['external_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_time_entries');
    }
}
