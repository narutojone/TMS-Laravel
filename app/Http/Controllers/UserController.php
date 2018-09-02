<?php

namespace App\Http\Controllers;

use App\Repositories\CustomerType\CustomerType;
use App\Repositories\Group\Group;
use App\Repositories\OooReason\OooReason;
use App\Repositories\Option\OptionInterface;
use App\Repositories\OooReason\OooReasonInterface;
use App\Repositories\System\System;
use App\Repositories\Task\TaskInterface;
use App\Repositories\TaskType\TaskType;
use App\Repositories\User\ShowOooTasksRequest;
use App\Repositories\Task\Task;
use App\Repositories\User\User;
use App\Repositories\User\UserCreateRequest;
use App\Repositories\User\UserInterface;
use App\Repositories\User\UserUpdateRequest;
use App\Repositories\UserOutOutOffice\UserOutOutOffice;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @var $userRepository - EloquentRepositoryUser
     */
    private $userRepository;

    /**
     * @var $oooReasonRepository - EloquentRepositoryOooReason
     */
    private $oooReasonRepository;

    /**
     * @var $taskRepository - EloquentRepositoryTask
     */
    private $taskRepository;
    
    /**
    * UserController constructor.
    *
    * @param UserInterface $userRepository
    */
    public function __construct(UserInterface $userRepository, OooReasonInterface $oooReasonRepository, TaskInterface $taskRepository)
    {
        parent::__construct();

        $this->userRepository = $userRepository;
        $this->oooReasonRepository = $oooReasonRepository;
        $this->taskRepository = $taskRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $type = null)
    {
        // Make users
        $users = $this->userRepository->make();

        if (is_null($type)) {
            // Fetch all active users
            $users->active()
                ->orderBy('role', 'DESC')
                ->orderBy('pf_id')
                ->orderBy('name');
        } elseif ($type == 'deactivated'){
            // Fetch all deactivated users
            $users->deactivated()
                ->orderBy('name');
        }

        return view('users.index')->with([
            'users' => $users->paginate(50),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users.create')->with([
            'roles' => User::$availableRoles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Repositories\User\UserCreateRequest
     * 
     * @return \Illuminate\Http\Response
     */
    public function store(UserCreateRequest $request)
    {
        $user = $this->userRepository->create($request->all());
         
        return redirect()
            ->action('UserController@index')
            ->with('success', 'User created.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Repositories\User\User  $user
     * 
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, User $user)
    {
        $internalTasks = [];
        // Return internal client and it's tasks
        if($user->client_id) {
            $internalTasks = $this->taskRepository->make()->with('user')
                ->where('client_id', $user->client_id)
                ->uncompleted()
                ->prioritized()
                ->filterPrivate()
                ->get();
        }

        $tasks = $user->tasks()
            ->uncompleted()
            ->prioritized()
            ->get();

        $thisWeekTasks = $nextWeekTasks = $threeWeeksTasks = $fourWeeksTasks = collect([]);
        if ($tasks->count()) {
            $thisWeekTasks = $tasks->where('due_at', '>=', Carbon::now()->startOfWeek())
                ->where('due_at', '<=', Carbon::now()->endOfWeek());
            $nextWeekTasks = $tasks->where('due_at', '>=', Carbon::now()->startOfWeek()->addWeek())
                ->where('due_at', '<=', Carbon::now()->endOfWeek()->addWeek());
            $threeWeeksTasks = $tasks->where('due_at', '>=', Carbon::now()->startOfWeek()->addWeek(2))
                ->where('due_at', '<=', Carbon::now()->endOfWeek()->addWeek(2));
            $fourWeeksTasks = $tasks->where('due_at', '>=', Carbon::now()->startOfWeek()->addWeek(3))
                ->where('due_at', '<=', Carbon::now()->endOfWeek()->addWeek(3));
        }

        return view('users.show')->with([
            'user'            => $user,
            'tasks'           => $internalTasks,
            'clients'         => $user->clients()->paginate(25),
            'outOfOffice'     => UserOutOutOffice::with(['reason'])->where('user_id', $user->id)->get(),
            'thisWeekTasks'   => $thisWeekTasks,
            'nextWeekTasks'   => $nextWeekTasks,
            'threeWeeksTasks' => $threeWeeksTasks,
            'fourWeeksTasks'  => $fourWeeksTasks,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Repositories\User\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $optionRepository = app()->make(OptionInterface::class);
        $groupForYearlyStatements = $optionRepository->model()->where('key', '=', 'group_yearly_statements_field')->first()->group;

        return view('users.edit')->with([
            'user'                      => $user,
            'userGroups'                => $user->groups->pluck('id')->toArray(),
            'customerTypes'             => CustomerType::pluck('name','id')->toArray(),
            'systems'                   => System::visible()->pluck('name','id')->toArray(),
            'taskTypes'                 => TaskType::pluck('name','id')->toArray(),
            'groups'                    => Group::pluck('name', 'id')->toArray(),
            'groupForYearlyStatements'  => $groupForYearlyStatements,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Repositories\User\UserUpdateRequest;  $request
     * @param  \App\Repositories\User\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        $this->userRepository->update($user->id, $request->all());

        return redirect()
            ->action('UserController@show', $user)
            ->with('success', 'User updated.');
    }

    /**
     * Activate user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Repositories\User\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activate(Request $request, User $user)
    {
        $user = $this->userRepository->activate($user->id);

        // Active internal project
        if($user->client_id){
            $user->internalClient()->update([
                'manager_id'  => $user->id,
                'employee_id' => $user->id,
                'active'      => 1,
            ]);
        }

        return redirect()
            ->action('UserController@index')
            ->with('success', 'User activated.');
    }

    /**
     * Deactivate user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Repositories\User\User  $user
     * @return \Illuminate\Http\Response
     */
    public function deactivate(Request $request, User $user)
    {
        $user = $this->userRepository->deactivate($user, $request->all(), $request->user()->id);

        // Deactivate internal project
        if($user->client_id){
            $user->internalClient()->update([
                'manager_id'  => null,
                'employee_id' => null,
                'active'      => 0,
            ]);
        }
        
        return redirect()
            ->action('UserController@index')
            ->with('info', 'User deactivated.');
    }

    /**
     * Create out of office period for users.
     *
     * @param Request $request
     * @param User $user
     * 
     * @return void
     */
    public function createOutOfOffice(Request $request, User $user)
    {
        // Validate access rules
        if($request->user()->id != $user->id && !$request->user()->hasRole(User::ROLE_ADMIN)) {
            abort(403, 'Unauthorized');
        }

        $reasons = $this->oooReasonRepository->make()->orderBy('name', 'ASC')->get();

        return view('users.out-of-office')->with([
            'user'    => $user,
            'reasons' => $reasons,
        ]);
    }

    /**
     * Show a list of tasks that will become overdoe for a given period of time.
     *
     * @param ShowOooTasksRequest $request
     * @param User $user
     * @return void
     */
    public function showOooTasks(ShowOooTasksRequest $request, User $user)
    {
        $fromDate = $request->input('from');
        $toDate = $request->input('to');

        $taskRepository = \App::make('App\Repositories\Task\TaskInterface');
        $tasks = $taskRepository->getTasksThatWillBecomeOverdue($user, $fromDate, $toDate);
        
        return view('users.out-of-office-overdue-tasks')->with([
            'tasks'  => $tasks,
            'from'   => $fromDate,
            'to'     => $toDate,
            'reason' => $request->input('reason'),
            'user'   => $user,
        ]);
    }

    /**
     * Save a user as out of office.
     *
     * @param Request $request
     * @param User $user
     * @return void
     */
    public function storeOutOfOffice(Request $request, User $user)
    {
        $fromDate = $request->input('from');
        $toDate = $request->input('to');
        $reason = $request->input('reason');

        $taskRepository = \App::make('App\Repositories\Task\TaskInterface');
        $tasks = $taskRepository->getTasksThatWillBecomeOverdue($user, $fromDate, $toDate);
        
        $taskIds = [];
        foreach($tasks as $task) {
            $taskIds[] = $task->id;
        }

        $userOutOfOfficeAttributes = [
            'user_id'        => $user->id,
            'reason_id'      => $reason,
            'from_date'      => $fromDate,
            'to_date'        => $toDate,
            'accepted_tasks' => json_encode($taskIds),
        ];
        
        $userOutOutOfficeRepository = \App::make('App\Repositories\UserOutOutOffice\UserOutOutOfficeInterface');
        $userOutOutOfficeRepository->create($userOutOfOfficeAttributes);

        return redirect(url('/dashboard'))->with('success', 'Out of office period created');
    }

    /**
     * Remove out of office period for a user
     *
     * @param Request $request
     * @param User $user
     * @return void
     */
    public function removeOoo(Request $request, User $user)
    {
        $userOutOutOfficeRepository = \App::make('App\Repositories\UserOutOutOffice\UserOutOutOfficeInterface');

        // Check if the out of office period is valid
        $userOutOfOffice = $userOutOutOfficeRepository->make()
            ->where('id', $request->get('ooo', 0))
            ->where('user_id', $user->id)
            ->where('from_date', '>', Carbon::now()->toDateString())
            ->first();

        if(!$userOutOfOffice) {
            return back()->with('error', 'Invalid out of office period');
        }

        $userOutOutOfficeRepository->delete($userOutOfOffice->id);
        return back()->with('success', 'Out of office period removed');
    }

    /**
     * End current out of office period
     *
     * @param Request $request
     * @param User $user
     * @return void
     */
    public function endCurrentOoo(Request $request, User $user)
    {
        $userOutOutOfficeRepository = \App::make('App\Repositories\UserOutOutOffice\UserOutOutOfficeInterface');
        $now = Carbon::now();

        // Check if user is currently in out of office period
        $userOutOfOffice = $userOutOutOfficeRepository->make()
            ->where('user_id', $user->id)
            ->where('from_date', '<=', $now)
            ->where('to_date', '>=', $now)
            ->first();

        if(!$userOutOfOffice) {
            return back()->with('error', 'User is not currently out of office');
        }

        // Update user ooo period termination date
        $userOutOutOfficeRepository->update($userOutOfOffice->id, ['to_date'=>$now->subDay(1)]);

        $this->userRepository->update($user->id, ['out_of_office'=>0]);
        // Update user out_of_office flag

        return back()->with('success', 'Out of office period terminated');
    }

    /**
     * Shows user's tasks of specific week
     *
     * @param Request $request
     * @param User $user
     * @param $week
     *
     * @return \Illuminate\Http\Response
     */
    public function weekTasks(Request $request, User $user, $week)
    {
        $tasks = $user->tasks()
            ->uncompleted()
            ->prioritized()
            ->where('due_at', '>=', Carbon::now()->addWeek($week - 1)->startOfWeek())
            ->where('due_at', '<=', Carbon::now()->addWeek($week - 1)->endOfWeek())
            ->get();

        switch ($week) {
            case '1':
                $breadcrumbItem = 'This week tasks';
                break;
            case '2':
                $breadcrumbItem = 'Next week tasks';
                break;
            case '3':
                $breadcrumbItem = 'Three weeks tasks';
                break;
            case '4':
                $breadcrumbItem = 'Four weeks tasks';
                break;
            default:
                $breadcrumbItem = 'Tasks';
                break;
        }

        return view('users.week-tasks')->with([
            'user'                   => $user,
            'tasks'                  => $tasks,
            'breadcrumbItem'         => $breadcrumbItem,
        ]);
    }
}
