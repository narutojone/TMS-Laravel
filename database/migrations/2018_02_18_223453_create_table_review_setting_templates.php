<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableReviewSettingTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('review_setting_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('review_setting_id')->unsigned()->references('id')->on('review_settings');
            $table->integer('template_id')->unsigned()->references('id')->on('templates');
            $table->softDeletes();
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
