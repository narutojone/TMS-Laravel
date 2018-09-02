<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameHasPullRequestColumnInGithubIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('github_issues', function (Blueprint $table) {
            $table->renameColumn('has_pull_request', 'pull_request');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('github_issues', function (Blueprint $table) {
            $table->renameColumn('pull_request', 'has_pull_request');
        });
    }
}
