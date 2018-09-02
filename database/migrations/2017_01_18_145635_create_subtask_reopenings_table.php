<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubtaskReopeningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subtask_reopenings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('subtask_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->text('reason')->nullable();
            $table->datetime('completed_at');
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
        Schema::dropIfExists('subtask_reopenings');
    }
}
