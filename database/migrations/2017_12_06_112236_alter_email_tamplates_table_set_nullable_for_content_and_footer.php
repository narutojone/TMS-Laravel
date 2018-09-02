<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEmailTamplatesTableSetNullableForContentAndFooter extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_templates', function (Blueprint $table) {
            $table->text('content')->nullable()->change();
            $table->text('content_html')->nullable()->change();
            $table->text('footer')->nullable()->change();
            $table->text('footer_html')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email_templates', function (Blueprint $table) {
            $table->text('content')->nullable(false)->change();
            $table->text('content_html')->nullable(false)->change();
            $table->text('footer')->nullable(false)->change();
            $table->text('footer_html')->nullable(false)->change();
        });
    }
}
