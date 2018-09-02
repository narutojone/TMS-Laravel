<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertClientRatingToSubtaskModuleTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('subtask_module_templates')->insert([
            'name'          => 'Rate client',
            'description'   => 'Get user feedback to rate the client.',
            'template'      => 'client-rating',
            'user_input'    => 1,
            'settings'      => json_encode([]),
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
