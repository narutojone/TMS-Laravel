<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GrahamCampbell\GitHub\Facades\GitHub;
use App\Repositories\GithubMilestone\GithubMilestoneInterface;

class FetchGitHubMilestones extends Command
{
    protected $signature = 'github:fetch-milestones';

    protected $description = 'Fetch GitHub milestones and save them into TMS database';

    protected $gitHubMilestoneRepository;

    public function __construct(GithubMilestoneInterface $gitHubMilestoneRepository)
    {
        parent::__construct();

        $this->gitHubMilestoneRepository = $gitHubMilestoneRepository;
    }

    public function handle()
    {
        $milestones = $this->grabGitHubMilestones();
        if ($milestones) {
            foreach ($milestones as $milestone) {
                $this->gitHubMilestoneRepository->model()->firstOrCreate($milestone, $milestone);
            }
        }
    }

    private function grabGitHubMilestones()
    {
        $page = 1;
        $perPage = 30;
        $issues = [];
        $nextPage = true;

        $this->alert("Getting all milestones from GH");

        while ($nextPage) {

            $this->info("Get GH milestones from page # $page");
            $apiResponse = GitHub::connection('main')->issue()->milestones()->all(env('GITHUB_USERNAME'), env('GITHUB_REPO'), ['page' => $page, 'state' => 'all']);

            $count = count($apiResponse);
            $this->comment(" >> found $count records.");

            // Exit from the loop if script moved to the next page, but there are no records.
            if ($count == 0) {
                break;
            }

            $transformResponse = collect($apiResponse)->map(function ($item) {
                return [
                    'number' => $item['number'],
                    'title' => $item['title'],
                ];
            })->all();

            unset($apiResponse);
            $issues = array_merge($issues, $transformResponse);

            if ($count < $perPage) {
                $nextPage = false;
            } else {
                $page++;
            }

            usleep(500000);
        }

        $this->alert("Total milestones found: " . count($issues));

        return $issues;
    }
}

