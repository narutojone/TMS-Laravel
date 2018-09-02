<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRatingTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rating_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('subject', ['client', 'user']);
            $table->string('email_template');
            $table->integer('tasks_completed');
            $table->integer('days_from_last_review');
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
        Schema::dropIfExists('rating_templates');
    }
}
