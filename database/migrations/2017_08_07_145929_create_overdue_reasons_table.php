<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOverdueReasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('overdue_reasons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('reason');
            $table->boolean('required')->default(false);
            $table->integer('priority')->unsigned();
            $table->boolean('visible')->default(false);
            $table->string('hex')->default('#ED5565');
            $table->boolean('active')->default(true);
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
        Schema::dropIfExists('task_overdue_reasons');
    }
}
