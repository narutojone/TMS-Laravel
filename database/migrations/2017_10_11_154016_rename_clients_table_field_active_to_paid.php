<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameClientsTableFieldActiveToPaid extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->renameColumn('active', 'paid');
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->tinyInteger('active')->after('paid')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('active');
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->renameColumn('paid', 'active');
        });
    }
}
