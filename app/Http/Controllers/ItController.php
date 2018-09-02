<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\GithubIssue\GithubIssue;
use App\Repositories\GithubIssue\GithubIssueInterface;
use App\Repositories\GithubMilestone\GithubMilestoneInterface;
use App\Repositories\HarvestDevTimeEntry\HarvestDevTimeEntryInterface;

class ItController extends Controller
{
    protected $gitHubIssueRepository;
    protected $harvestDevTimeEntryRepository;

    public function __construct(GithubIssueInterface $gitHubIssueRepository, HarvestDevTimeEntryInterface $harvestDevTimeEntry)
    {
        $this->middleware('admin_only');
        parent::__construct();

        $this->gitHubIssueRepository = $gitHubIssueRepository;
        $this->harvestDevTimeEntryRepository = $harvestDevTimeEntry;
    }

    public function issuesList(Request $request)
    {
        $milestoneId = $request->get('milestone_id');
        $state = $request->get('state');
        $type = $request->get('type');
        
        $query = $this->gitHubIssueRepository->model()
            ->with(['harvestTimeEntities'])
            ->milestone($milestoneId)
            ->state($state)
            ->where('pull_request', ($type ? GithubIssue::IS_PULL_REQUEST : GithubIssue::NOT_PULL_REQUEST))
            ->orderBy('issue_number', 'DESC');
        if (is_null($milestoneId)) {
            // Generate the page path with filter parameters
            $path = url()->current() .
            '?state=' . $state .
            '&type='  . $type;

            $issues = $query->paginate(30)->withPath($path);
        } else {
            $issues = $query->get();
        }

        $gitHubMilestoneRepository = app()->make(GithubMilestoneInterface::class);
        $milestonesList = $gitHubMilestoneRepository->model()->get();

        // Building hit rate statistical data
        $hitRate = 0;
        $summarizedEstimations = 0;
        $summarizedTracked = 0;

        // Fetch all estimations and tracked hours within chosen scope
        foreach ($issues as $issue) {
            $summarizedEstimations += $issue->issue_estimate;
            $summarizedTracked += $issue->tracked;
        }

        // If we have a divisor calculate hit rate
        if ($summarizedEstimations) {
            // Calculate hitrate percentage and round to nearest two decimals
            $hitRate = round(($summarizedTracked / $summarizedEstimations) * 100, 2);
        }

        return view('reports.it.github_issues')->with([
            'issues' => $issues,
            'milestoneId' => $milestoneId,
            'milestonesList' => $milestonesList,
            'hitRate' => $hitRate,
        ]);
    }

    public function unmatchedTimeEntries()
    {
        $timeEntries = $this->harvestDevTimeEntryRepository->model()->active()->whereNull('github_issue')->paginate(30);

        return view('reports.it.unmatched_harvest_time_entries', compact('timeEntries'));
    }

    public function assignTimeEntityForm(int $id)
    {
        $timeEntity = $this->harvestDevTimeEntryRepository->model()->find($id);
        $gitHubIssues = $this->gitHubIssueRepository->model()->get();
        $autoCompleteSource = json_encode($this->gitHubIssueRepository->createJsonFormatForAutoComplete());

        return view('reports.it.assign_time_entity_form')->with([
            'timeEntity' => $timeEntity,
            'gitHubIssues' => $gitHubIssues,
            'autoCompleteSource' => $autoCompleteSource,
        ]);
    }

    public function githubIssuesList()
    {
        return response()->json($this->gitHubIssueRepository->createJsonFormatForAutoComplete());
    }

    public function assignTimeEntity(Request $request, int $id)
    {
        $update = $this->harvestDevTimeEntryRepository->update($id, [
            'github_issue' => $request->get('github-issue-id')
        ]);

        if ($update) {
            return redirect()->route('reports.it.unmatched_time')->with('message', 'Time entry successfully assigned.');
        } else {
            abort(500, 'Something went wrong');
        }
    }

    public function disregardTimeEntity(Request $request, int $id)
    {
        $update = $this->harvestDevTimeEntryRepository->update($id, [
            'ignored' => true
        ]);

        if ($update) {
            return response()->json(['status' => true]);
        } else {
            abort(500, 'Something went wrong');
        }
    }

    public function githubIssueTimeEntries(GithubIssue $githubIssue)
    {
        $githubIssue->load(['harvestTimeEntities']);

        return view('reports.it.github_issue_time_entities_list')->with([
            'githubIssue' => $githubIssue,
        ]);
    }
}
