<?php

namespace App\Http\Controllers;

use App\Repositories\User\UserInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Repositories\OverdueReason\OverdueReason;
use App\Repositories\TaskOverdueReason\TaskOverdueReason;
use App\Repositories\User\User;
use App\Repositories\Task\Task;
use Illuminate\Support\Facades\DB;
use App\Repositories\OverdueReason\OverdueReasonInterface;
use App\Repositories\OverdueReason\OverdueReasonCreateRequest;
use App\Repositories\OverdueReason\OverdueReasonUpdateRequest;
use App\Repositories\OverdueReason\OverdueReasonMoveRequest;

class OverdueReasonController extends Controller
{
    /**
     * @var $overdueReasonRepository - EloquentRepositoryOverdueReason
     */
    private $overdueReasonRepository;

    /**
     * Instantiate a new controller instance.
     *
     * @param OverdueReasonInterface $overdueReasonRepository
     */
    public function __construct(OverdueReasonInterface $overdueReasonRepository)
    {
        $this->middleware('admin_only');
        parent::__construct();

        $this->overdueReasonRepository = $overdueReasonRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $reasons = $this->overdueReasonRepository->make()->orderBy('priority')->get();

        return view('overdue.index')->with([
            'reasons'    => $reasons,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $userRepository = app()->make(UserInterface::class);
        $users = $userRepository->activeUsers();

        return view('overdue.create')->with([
            'users'     => $users,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Repositories\OverdueReason\OverdueReasonCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OverdueReasonCreateRequest $request)
    {
        $reason = $this->overdueReasonRepository->create($request->all());

        return redirect()
            ->action('OverdueReasonController@index')
            ->with('success', 'Reason created.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Repositories\OverdueReason\OverdueReason  $reason
     * @return \Illuminate\Http\Response
     */
    public function edit(OverdueReason $reason)
    {
        $userRepository = app()->make(UserInterface::class);
        $users = $userRepository->activeUsers();

        return view('overdue.edit')->with([
            'reason'    => $reason,
            'users'     => $users,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param OverdueReasonUpdateRequest $request
     * @param  int $id - id of the resource that is going to be updated
     * @return \Illuminate\Http\Response
     */
    public function update(OverdueReasonUpdateRequest $request, int $id)
    {
        $reason = $this->overdueReasonRepository->update($id, $request->all());

        return redirect()
            ->action('OverdueReasonController@index')
            ->with('success', 'Reason updated.');
    }

    /**
     * Delete the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id - id of the resource that is going to be deleted (deleted means updated with the appropiate value for column active)
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, int $id)
    {
        $reason = $this->overdueReasonRepository->make()->find($id);
        $input = $reason->toArray();

        if ($request->input('active') == 1){
            $input['active'] = false;
            $message = 'Reason deactivated.';
        } else {
            $input['active'] = true;
            $message = 'Reason activated.';
        }
        
        $reason = $this->overdueReasonRepository->update($id, $input);
        
        return redirect()
            ->action('OverdueReasonController@index')
            ->with('success', $message);
    }

    /**
     * Move the reason up or down.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id - id of the resource
     * @return \Illuminate\Http\Response
     */
    public function move(OverdueReasonMoveRequest $request, int $id)
    {
        $direction = $request->input('direction');
        $reason = $this->overdueReasonRepository->move($id, $direction);

        return redirect()
            ->action('OverdueReasonController@index')
            ->with('success', 'Reason moved ' . $direction . '.');
    }

    /**
     * Show Overdue report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function report(Request $request) {
        $overdues = [];
        $tasksWithoutReason = 0;

        $overduesRaw = DB::select(DB::raw("
            SELECT 
                users.id as userId, users.name as userName,
                tor.reason_id as reasonId,
                count(t.id) as countTaskId
            FROM users
            LEFT JOIN tasks t
                ON users.id = t.user_id
            LEFT JOIN task_overdue_reasons tor
                ON t.id = tor.task_id 
                AND tor.active = 1
            WHERE 
                t.active = 1 AND t.`deadline` < DATE(NOW()) AND t.completed_at is NULL
            GROUP BY users.id, tor.reason_id
            ORDER BY userId, reasonId ASC
        "));


        foreach($overduesRaw as $overdueRaw) {
            if(is_null($overdueRaw->reasonId)) {
                $reasonId = 0;
                $tasksWithoutReason += $overdueRaw->countTaskId;
            }
            else {
                $reasonId = $overdueRaw->reasonId;
            }

            $overdues[$overdueRaw->userId][$reasonId] = $overdueRaw->countTaskId;
        }


        $users = Task::overdue()
            ->whereNotNull('user_id')
            ->where('active',1)
            ->with('user')
            ->groupBy('user_id')
            ->select('user_id')
            ->get()
            ->pluck('user');

        return view('reports.overdue.index')->with([
            'overdues'          => $overdues,
            'total'             => $tasksWithoutReason,
            'users'             => $users,
            'overdueReasons'    => OverdueReason::where('overdue_reasons.is_visible_in_report', 1)->get(),
        ]);
    }

    /**
     * Show Overdue report category.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function reportWithoutReason(Request $request) {
        $users = Task::overdue()
            ->whereNotNull('user_id')
            ->where('active', 1)
            ->with('user')
            ->groupBy('user_id')
            ->select('user_id')
            ->get()
            ->pluck('user');

        $tasks = Task::select(['tasks.*'])
            ->with(['client', 'user', 'overdueReason.overdueReason', 'subtasks', 'activeSubtasks', 'template'])
            ->leftJoin('task_overdue_reasons', function ($join) {
                $join->on('tasks.id', '=', 'task_overdue_reasons.task_id');
                $join->where('task_overdue_reasons.active', 1);
            })
            ->where('tasks.active', 1)
            ->whereNull('tasks.completed_at')
            ->whereNull('task_overdue_reasons.id')
            ->where('tasks.deadline', '<', Carbon::now())
            ->orderBy('due_at');

        if ($request->filled('user')) {
            $tasks->where('tasks.user_id', $request->input('user'));
        }

        $tasks = $tasks->get();

        return view('reports.overdue.noReason')
            ->with([
                'tasks' => $tasks,
                'users' => $users,
                'selectedUser' => $request->input('user'),
            ]);
    }

    /**
     * Show User Overdue report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Repositories\User\User  $user
     * @return \Illuminate\Http\Response
     */
    public function reportReason(Request $request, User $user) {
        $reason = $request->get('reason', null);
        $tasks = Task::select(['tasks.*'])
                    ->with(['client', 'user','overdueReason.overdueReason', 'subtasks', 'activeSubtasks', 'template'])
                    ->leftJoin('task_overdue_reasons', function($join) use ($reason)
                    {
                        $join->on('tasks.id', '=', 'task_overdue_reasons.task_id');
                        $join->where('task_overdue_reasons.active', 1);
                        if(!is_null($reason)) {
                            $join->where('task_overdue_reasons.reason_id', $reason);
                        }
                    })
                    ->where('tasks.active', 1)
                    ->where('tasks.user_id', $user->id)
                    ->whereNull('tasks.completed_at')
                    ->where('tasks.deadline', '<', Carbon::now());



        if (!is_null($reason)) {
            $tasks->whereNotNull('task_overdue_reasons.id');
        }

        $path = url()->current() .
            '?reason=' . $request->input('reason');

        return view('reports.overdue.reason')
            ->with([
                'tasks' => $tasks->paginate(25)->withPath($path),
                'user' => $user,
                'reason' => OverdueReason::find($request->input('reason')),
                'selectedReason' => $request->input('reason'),
            ]);
    }

    /**
     * Show aggregated result of number of overdue reasons by reason id
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function reportReasonWithFilters(Request $request)
    {
        // Query all overdue reasons on tasks with overdue reason
        $overduereasons = TaskOverdueReason::join(
            'overdue_reasons',
            'overdue_reasons.id',
            'task_overdue_reasons.reason_id'
        );

        // Filter by user
        if ($request->filled('user')) {
            $overduereasons->where('task_overdue_reasons.user_id', $request->input('user'));
        }

        // Filter by from date
        if ($request->filled('from')) {
            $overduereasons->where('task_overdue_reasons.created_at', '>=' ,$request->input('from'));
        }

        // Filter by to date
        if ($request->filled('to')) {
            $overduereasons->where('task_overdue_reasons.created_at', '<=' ,$request->input('to'));
        }

        // Return the counts
        $overduereasons->selectRaw('
                overdue_reasons.id as id,
                overdue_reasons.reason as title,
                count(task_overdue_reasons.reason_id) as count
            ')
            ->groupBy('task_overdue_reasons.reason_id')
            ->orderBy('count', 'DESC');

        // Get total overdue reasons count
        $count = $overduereasons->get()->sum('count');

        // Get the available users
        $users = User::active()->get();

        // Generate the page path with filter parameters
        $path = url()->current() .
            '?user=' . $request->input('user') .
            '&from=' . $request->input('from') .
            '&to=' . $request->input('to');

        return view('reports.overdue.reasonFilters')->with([
            'users' => $users,
            'selectedUser' => $request->input('user'),
            'selectedFromDate' => $request->input('from'),
            'selectedToDate' => $request->input('to'),
            'overduereasons' => $overduereasons->paginate(25)->withPath($path),
            'count' => $count,
        ]);
    }
}
