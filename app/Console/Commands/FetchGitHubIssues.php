<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use GrahamCampbell\GitHub\Facades\GitHub;
use App\Repositories\GithubIssue\GithubIssueInterface;
use App\Repositories\GithubMilestone\GithubMilestoneInterface;

class FetchGitHubIssues extends Command
{
    protected $signature = 'github:fetch-issues';

    protected $description = 'Fetch GitHub issues and their estimate from ZenHub';

    protected $gitHubIssueRepository;

    protected $gitHubMilestoneRepository;

    protected $guzzleClient;

    public function __construct(GithubIssueInterface $gitHubIssueRepository, GithubMilestoneInterface $gitHubMilestoneRepository, Client $guzzleClient)
    {
        parent::__construct();

        $this->guzzleClient = $guzzleClient;
        $this->gitHubIssueRepository = $gitHubIssueRepository;
        $this->gitHubMilestoneRepository = $gitHubMilestoneRepository;
    }

    public function handle()
    {
        // Get all issues from GitHub API
        $gitHubIssuesList = $this->grabGitHubIssues();

        $index = 1;
        $updated = 0;
        $created = 0;

        foreach ($gitHubIssuesList as $gitHubIssue) {

            // Get estimate for specified issue from ZenHub API
            $zenHubEstimate = $this->fetchEstimateFromZenHub($gitHubIssue['number']);

            // Get TMS milestone ID
            $milestoneId = $this->getMilestoneId($gitHubIssue['milestone']);

            // Find issue by number in TMS db
            $issueLogMessage = "* $index * Find issue in TMS #" . $gitHubIssue['number'];
            $findIssue = $this->gitHubIssueRepository->model()->where('issue_number', $gitHubIssue['number'])->first();
            if ($findIssue) {

                $issueLogMessage .= " -> Issue was found (ID: $findIssue->id)";
                // This need to be done for isDirty() method
                $findIssue->fill([
                    'issue_estimate' => $zenHubEstimate,
                    'issue_title' => $gitHubIssue['title'],
                    'milestone_id' => $milestoneId,
                    'pull_request' => $gitHubIssue['pull_request'],
                    'state' => $gitHubIssue['state'],
                ]);

                // Update estimate and title only if this data have been changed
                if ($findIssue->isDirty(['issue_estimate', 'issue_title', 'milestone_id', 'pull_request', 'state'])) {

                    $this->gitHubIssueRepository->update($findIssue->id, [
                        'issue_estimate' => $zenHubEstimate,
                        'issue_title' => $gitHubIssue['title'],
                        'milestone_id' => $milestoneId,
                        'pull_request' => $gitHubIssue['pull_request'],
                        'state' => $gitHubIssue['state'],
                    ]);
                    $updated++;
                    $issueLogMessage .= " -> Issue data was changed, so we updated it";

                } else {
                    $issueLogMessage .= " -> Issue data wasn't changed";
                }

            } else {
                $newIssue = $this->gitHubIssueRepository->create([
                    'issue_id' => $gitHubIssue['id'],
                    'issue_number' => $gitHubIssue['number'],
                    'issue_estimate' => $zenHubEstimate,
                    'issue_title' => $gitHubIssue['title'],
                    'milestone_id' => $milestoneId,
                    'pull_request' => $gitHubIssue['pull_request'],
                    'state' => $gitHubIssue['state'],
                ]);
                $created++;

                $issueLogMessage .= " -> New issue was created (ID: $newIssue->id)";
            }

            $this->comment($issueLogMessage);
            $index++;
        }

        $this->info("Total updated: $updated");
        $this->info("Total created: $created");

    }

    /**
     * Get all issues from GitHub API
     *
     * @return array
     */
    private function grabGitHubIssues()
    {
        $page = 1;
        $perPage = 30;
        $issues = [];
        $nextPage = true;

        $this->alert("Getting all issues from GH");

        while ($nextPage) {

            $this->info("Get GH issues from page # $page");
            $apiResponse = GitHub::connection('main')->issues()->all(env('GITHUB_USERNAME'), env('GITHUB_REPO'), ['page' => $page, 'state' => 'all', 'assignee' => '*']);

            $count = count($apiResponse);
            $this->comment(" >> found $count records.");

            // Exit from the loop if script moved to the next page, but there are no records.
            if ($count == 0) {
                break;
            }

            $transformResponse = collect($apiResponse)->map(function ($item) {
                return [
                    'id' => $item['id'],
                    'number' => $item['number'],
                    'title' => $item['title'],
                    'state' => $item['state'],
                    'closed_at' => $item['closed_at'],
                    'pull_request' => (int)array_key_exists('pull_request', $item),
                    'milestone' => $item['milestone'],
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

        $this->alert("Total issues found: " . count($issues));

        return $issues;
    }

    /**
     * Find milestone and return ID
     *
     * @param $milestone
     * @return null
     */
    public function getMilestoneId($milestone)
    {
        if ($milestone) {

            $findMilestoneByNumber = $this->gitHubMilestoneRepository->model()->where('number', $milestone['number'])->first();
            if ($findMilestoneByNumber) {
                return $findMilestoneByNumber->id;
            }
        }

        return null;
    }

    /**
     * Get estimate for specified issue from ZenHub API
     *
     * @param $issueId
     * @return float|null
     */
    protected function fetchEstimateFromZenHub($issueId)
    {
        // DUE API LIMIT (100 requests per second)
        usleep(650000);
        $repoId = env('GITHUB_REPO_ID');
        $url = sprintf('https://api.zenhub.io/p1/repositories/%s/issues/%s', $repoId, $issueId);
        $res = $this->guzzleClient->request('GET', $url, [
            'headers' => [
                'X-Authentication-Token' => env('ZENHUB_TOKEN'),
            ]
        ]);

        if ($res->getStatusCode()) {
            $response = json_decode($res->getBody()->getContents(), true);
            if (array_key_exists('estimate', $response)) {
                return floatval($response['estimate']['value']);
            }
        }
        return null;
    }
}
