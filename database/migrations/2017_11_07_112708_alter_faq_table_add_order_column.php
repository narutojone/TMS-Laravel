<?php

use App\Repositories\Faq\Faq;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFaqTableAddOrderColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('faq', function (Blueprint $table) {
            $table->integer('order')->unsigned();
        });

        Faq::all()->each(function ($faq, $key) {
            $faq->order = ++$key;
            $faq->save();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('faq', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
}
