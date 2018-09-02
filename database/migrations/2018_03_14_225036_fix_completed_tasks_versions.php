<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Repositories\Template\TemplateInterface;
use App\Repositories\TemplateVersion\TemplateVersionInterface;

class FixCompletedTasksVersions extends Migration
{
    // Vor all templates => version 1 has created_at the same as the template creation date
    protected $templatesWithMoreVersions = [
        15 => [
            1 => '2017-03-07 23:34:00',
            2 => '2017-08-08 00:00:00',
            3 => '2017-07-10 00:00:00',
        ],
        31 => [
            1 => '2017-03-10 15:01:47',
            2 => '2017-09-26 00:00:00',
        ],
        35 => [
            1 => '2017-03-16 20:59:33',
            2 => '2017-12-07 00:00:00',
        ],
        71 => [
            1 => '2017-11-08 09:18:12',
            2 => '2017-11-22 03:50:11',
        ],
        72 => [
            1 => '2017-11-09 10:11:08',
            2 => '2018-02-02 00:00:00',
        ],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Prepare repos
        $templateRepository = app()->make(TemplateInterface::class);
        $templateVersionRepository = app()->make(TemplateVersionInterface::class);

        // Get all templates that have only 1 version
        $templates = $templateRepository->model()->get(['id', 'title', 'description', 'created_at']);
        foreach($templates as $template) {
            if ($template->versions->count() == 1) {
                $template->versions[0]->created_at = $template->created_at;
                $template->versions[0]->save();
            }
        }

        // Update manual dates from $this->templatesWithMoreVersions
        foreach($this->templatesWithMoreVersions as $templateId => $templateVersions) {
            foreach($templateVersions as $versionNumber => $createdAt) {
                $templateVersionRepository->model()->where('template_id', $templateId)->where('version_no', $versionNumber)->update([
                    'created_at' => \Carbon\Carbon::parse($createdAt),
                ]);
            }
        }

        // Update created_by to "Joachim"
        $templateVersionRepository->model()->update([
            'created_by' => 2,
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
