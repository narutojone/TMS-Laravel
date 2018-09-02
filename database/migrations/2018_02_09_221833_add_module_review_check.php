<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddModuleReviewCheck extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('subtask_module_templates')->insert([
            'name'          => 'Review check',
            'description'   => 'Check if a task that was created for a review, has the review completed before completing the task.',
            'template'      => 'review-check',
            'user_input'    => 0,
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
