<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInformationUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('information_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('information_id');
            $table->integer('user_id');
            $table->tinyInteger('accepted_status')->nullable();
            $table->timestamps();

            $table->index(['information_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('information_user');
    }
}
