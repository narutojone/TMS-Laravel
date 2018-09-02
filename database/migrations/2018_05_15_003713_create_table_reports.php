<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key', 255);
            $table->string('name', 255);
            $table->string('description', 255);
            $table->unsignedTinyInteger('active')->default(1);

            $table->timestamps();
        });

        DB::table('reports')->insert([
            'key'           => 'aggregated-overdue-report',
            'name'          => 'Aggregated Overdue Report',
            'description'   => 'Aggregated Overdue Report description',
            'active'        => 1,
        ]);
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
