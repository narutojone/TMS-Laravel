<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentationPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentation_pages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_page_id')->nullable()->unsigned()->index()->references('id')->on('documentation_pages');
            $table->text('title');
            $table->longText('content');
            $table->integer('order')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });
    }

//    /**
//     * Reverse the migrations.
//     *
//     * @return void
//     */
//    public function down()
//    {
//        Schema::dropIfExists('documentation_pages');
//    }
}
