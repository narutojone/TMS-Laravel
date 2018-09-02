<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableContracts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id')->unsigned();
            $table->unsignedTinyInteger('active')->default(1);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->unsignedTinyInteger('one_time')->default(0);
            $table->unsignedTinyInteger('under_50_bills')->default(0);
            $table->unsignedTinyInteger('shareholder_registry')->default(0);
            $table->unsignedTinyInteger('bank_reconciliation')->default(0);
            $table->date('bank_reconciliation_date')->nullable();
            $table->unsignedTinyInteger('bookkeeping')->default(0);
            $table->date('bookkeeping_date')->nullable();
            $table->unsignedTinyInteger('mva')->default(0);
            $table->string('mva_type', 10)->nullable();
            $table->unsignedTinyInteger('financial_statements')->default(0);
            $table->integer('financial_statements_year')->unsigned()->nullable();
            $table->unsignedTinyInteger('salary_check')->default(0);
            $table->unsignedTinyInteger('salary')->default(0);
            $table->unsignedInteger('created_by');

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
