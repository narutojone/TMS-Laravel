<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSettingsTableForReviews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('review_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('no_of_tasks_for_level_two')->unsigned()->default(5);
            $table->smallInteger('deadline_offset')->unsigned()->default(5);
            $table->integer('review_template_id')->unsigned();
            $table->integer('first_review_group_id')->unsigned();
            $table->integer('second_review_group_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('review_template_id')->references('id')->on('templates');
            $table->foreign('first_review_group_id')->references('id')->on('groups');
            $table->foreign('second_review_group_id')->references('id')->on('groups');
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
