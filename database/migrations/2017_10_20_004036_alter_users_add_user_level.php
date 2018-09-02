<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersAddUserLevel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('level')->after('email')->default(0);
        });

        DB::table('subtask_module_templates')->insert([
            'name' 			=> 'User tutorial',
            'description' 	=> 'User tutorial',
            'template' 		=> 'user-tutorial',
            'user_input' 	=> 0,
            'settings' 		=> json_encode([]),
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
