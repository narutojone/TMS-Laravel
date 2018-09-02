<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGithubIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('github_issues', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('issue_id');
            $table->unsignedInteger('issue_number');
            $table->unsignedInteger('milestone_id')->nullable()->default(null);;
            $table->boolean('has_pull_request')->default(0);
            $table->float('issue_estimate', 4, 2)->unsigned()->nullable()->default(null);
            $table->string('issue_title');
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
        Schema::dropIfExists('github_issues');
    }
}
