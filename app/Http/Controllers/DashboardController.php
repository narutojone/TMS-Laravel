<?php

namespace App\Http\Controllers;

use App\Repositories\OverdueReason\OverdueReasonInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Repositories\User\User;
use App\Repositories\Task\Task;
use Illuminate\Support\Facades\DB;
use App\Repositories\Client\Client;
use App\Repositories\Review\ReviewInterface;
use App\Repositories\OverdueReason\OverdueReason;
use App\Repositories\Review\EloquentRepositoryReview;

class DashboardController extends Controller
{

    var $overdueReasonRepository;

    public function __construct(OverdueReasonInterface $overdueReasonRepository)
    {
        parent::__construct();

        $this->overdueReasonRepository = $overdueReasonRepository;
    }

    public function dashboard(Request $request)
    {
        if ($request->user()->hasRole(User::ROLE_ADMIN)) {
            return redirect()->action('DashboardController@admin');
        } elseif ($request->user()->isManager()) {
            return redirect()->action('DashboardController@employee');
        }

        return redirect()->action('DashboardController@employee');
    }

    public function admin(Request $request)
    {
        // Query overdue tasks and order by deadline
        $tasks = Task::overdue()
            ->select(['tasks.*', DB::raw('any_value(overdue_reasons.priority) as priority'), DB::raw('any_value(ISNULL(priority)) as null_priority')])
            ->leftJoin(DB::raw('(SELECT * FROM task_overdue_reasons JOIN (SELECT MAX(task_overdue_reasons.id) max_id FROM task_overdue_reasons GROUP BY task_id) AS tor1 ON task_overdue_reasons.id = tor1.max_id) as tor'), 'tasks.id', '=', 'tor.task_id')
            ->leftJoin('overdue_reasons', 'overdue_reasons.id', '=', 'tor.reason_id')
            ->groupBy('tasks.id')
            ->orderBy('null_priority', 'asc')
            ->orderBy('priority', 'asc')
            ->where('tasks.active',1)
            ->with(['client', 'user', 'overdueReason'])
            ->orderBy('due_at', 'ASC');

        // Filter by user
        if ($request->filled('user')) {
            $tasks->where('tasks.user_id', $request->input('user'));
        }

        // Filter by client
        if ($request->filled('client')) {
            $tasks->where('client_id', $request->input('client'));
        }

        // Filter by category
        if ($request->filled('category')) {
            $tasks->where('category', $request->input('category'));
        }

        // Filter by comments
        if ($request->filled('comments')) {
            if ($request->input('comments') == 'yes') {
                $tasks->has('comments');
            } elseif ($request->input('comments') == 'no') {
                $tasks->has('comments', '<', 1);
            }
        }

        if ($request->filled('reason')) {
            if ($request->input('reason') === 'no_reason') {
                $tasks->with(['client', 'user','overdueReason.overdueReason', 'subtasks', 'activeSubtasks', 'template'])
                    ->leftJoin('task_overdue_reasons', function($join)
                    {
                        $join->on('tasks.id', '=', 'task_overdue_reasons.task_id');
                        $join->where('task_overdue_reasons.active', 1);
                    })
                    ->whereNull('tasks.completed_at')
                    ->whereNull('task_overdue_reasons.id')
                    ->where('tasks.deadline', '<', Carbon::now());
            } else {
                $tasks->where('tor.reason_id', $request->input('reason'))->where('tor.active', 1);
            }
        }

        // Get the available users
        $users = Task::overdue()
            ->whereNotNull('user_id')
            ->where('active',1)
            ->with('user')
            ->groupBy('user_id')
            ->select('user_id')
            ->get()
            ->pluck('user');

        // Get the available clients
        $clients = Task::overdue()
            ->with('client')
            ->has('client')
            ->where('active',1)
            ->groupBy('client_id')
            ->select('client_id')
            ->get()
            ->pluck('client');

        // Get the available categories
        $categories = Task::overdue()
            ->where('active',1)
            ->groupBy('category')
            ->select('category')
            ->get()
            ->pluck('category');

        // Generate the page path with filter parameters
        $path = url()->current() .
            '?user=' . $request->input('user') .
            '&client=' . $request->input('client') .
            '&category=' . $request->input('category') .
            '&reason=' . $request->input('reason');

        // Fetch clients with no manager or employee
        $clientsWithoutManager = Client::active()->paused(false)->whereNull('manager_id')->get();
        $clientsWithoutEmployee = Client::active()->paused(false)->whereNull('employee_id')->get();

        // Return the view with data
        return view('dashboard.admin')->with([
            'users' => $users,
            'clientsWithoutManager' => $clientsWithoutManager,
            'clientsWithoutEmployee' => $clientsWithoutEmployee,
            'selectedUser' => $request->input('user'),
            'clients' => $clients,
            'selectedClient' => $request->input('client'),
            'categories' => $categories,
            'selectedCategory' => $request->input('category'),
            'comments' => $request->input('comments'),
            'tasks' => $tasks->paginate(25)->withPath($path),
            'overdues' => OverdueReason::orderBy('priority')->get(),
            'selectedOverdue' => $request->input('reason'),
        ]);
    }

    public function manager(Request $request)
    {
        // Get available categories
        $categories = Task::groupBy('category')
            ->select('category')
            ->get()
            ->pluck('category');

        // Get available clients
        $clients = $request->user()->clientsManaging()->get();

        // Base task query
        $query = $request->user()->tasksManaging()->with(['client','activeSubtasks','overdueReason', 'overdueReason.reason'])->orderBy('due_at', 'ASC');

        // Do the filtering
        $query = $this->filter($query);

        // Get and paginate the unassigned tasks
        $unassigned = (clone $query)->whereNull('tasks.user_id')->paginate(25);

        // Get and paginate the overdue tasks
        $overdue = (clone $query)->overdue()->orderBy('active','DESC')->orderBy('deadline', 'ASC')->filterPrivate()->paginate(25);

        // Return the view with the data
        return view('dashboard.manager')->with([
            'categories' => $categories,
            'selectedCategory' => $request->input('category'),
            'clients' => $clients,
            'selectedClient' => $request->input('client'),
            'unassigned' => $unassigned,
            'overdue' => $overdue,
        ]);
    }

    public function employee(Request $request)
    {
        // Get available categories
        $categories = $request->user()->tasks()
            ->groupBy('category')
            ->select('category')
            ->get()
            ->pluck('category');

        // Get available clients
        $clients = $request->user()->getAccessibleClientsQuery()->get();

        // Base task query
        $query = $request->user()->tasks()->with(['client','activeSubtasks','overdueReason', 'overdueReason.reason'])->orderBy('due_at', 'ASC');

        // Do the filtering
        $query = $this->filter($query);

        // Queries for different time periods
        $overdue = $request->user()->overdueTasks()->orderBy('due_at', 'ASC')->get();

        $today = $request->user()->getTasksDueAt(
            Carbon::today()->startOfDay(),
            Carbon::today()->endOfDay()
        );

        $tomorrow = $request->user()->getTasksDueAt(
            Carbon::tomorrow()->startOfDay(),
            Carbon::tomorrow()->endOfDay()
        );

        $week = $request->user()->getTasksDueAt(
            Carbon::today()->addDays(2)->startOfDay(),
            Carbon::today()->addDays(7)->endOfDay()
        );

        $future = $request->user()->getTasksDueAt(
            Carbon::today()->addDays(8)->startOfDay()
        );

        // Filtering tasks by client and category
        if ($request->filled('client')) {
            $today->where('client_id', $request->input('client'));
            $tomorrow->where('client_id', $request->input('client'));
            $week->where('client_id', $request->input('client'));
            $future->where('client_id', $request->input('client'));
        }
        if ($request->filled('category')) {
            $week->where('category', $request->input('category'));
            $tomorrow->where('category', $request->input('category'));
            $week->where('category', $request->input('category'));
            $future->where('category', $request->input('category'));
        }

        // Get the tasks based on queries
        $today = $today->orderBy('due_at', 'ASC')->get();
        $tomorrow = $tomorrow->orderBy('due_at', 'ASC')->get();
        $week = $week->orderBy('due_at', 'ASC')->get();
        $future = $future->orderBy('due_at', 'ASC')->get();

        $delivered = (clone $query)->uncompleted()->delivered()->get();

        // Get all possible overdue reasons
        $overdues = $this->overdueReasonRepository->getAllActive();

        $unreadNotificationsQuery = (clone $query)->delivered()->whereNull('delivered_read_at');
        $unreadNotifications = $unreadNotificationsQuery->get();
        $unreadNotificationsQuery->update(['delivered_read_at' => Carbon::now()]);

        return view('dashboard.employee')->with([
            'categories'          => $categories,
            'selectedCategory'    => $request->input('category'),
            'clients'             => $clients,
            'selectedClient'      => $request->input('client'),
            'overdue'             => $overdue,
            'today'               => $today,
            'tomorrow'            => $tomorrow,
            'week'                => $week,
            'future'              => $future,
            'delivered'           => $delivered,
            'overdues'            => $overdues,
            'unreadNotifications' => $unreadNotifications,
        ]);
    }

    public function tasks(Request $request)
    {
        // Initialize tasks used by all filters
        $tasks = Task::select('tasks.*', 'clients.paid')
            ->with(['client', 'user'])
            ->join('clients', 'clients.id', '=', 'tasks.client_id')
            ->leftJoin(DB::raw('(SELECT * FROM task_overdue_reasons JOIN (SELECT MAX(task_overdue_reasons.id) max_id FROM task_overdue_reasons GROUP BY task_id) AS tor1 ON task_overdue_reasons.id = tor1.max_id) as tor'), 'tasks.id', '=', 'tor.task_id')
            ->leftJoin('overdue_reasons', 'overdue_reasons.id', '=', 'tor.reason_id')
            ->orderBy('tasks.due_at');

        // Fetch allowed clients
        $clients = $request->user()->getAccessibleClientsQuery()->pluck('id');
        $tasks = $tasks->filterPrivate()->whereIn('client_id', $clients);

        // Check task completed filter
        if ($request->input('completed') == 'Yes') {
            $tasks = $tasks->completed();
        }
        else {
            $tasks = $tasks->uncompleted();
        }

        // Filter by delivered
        if ($request->filled('delivered')) {
            $tasks->where('tasks.delivered', $request->input('delivered') == 'Yes' ? 1 : 0);
        }

        // Filter by paid
        if ($request->filled('paid')) {
            $tasks->where('clients.paid', $request->input('paid') == 'Yes' ? 1 : 0);
        }

        // Filter by user
        if ($request->filled('user')) {
            $tasks->where('tasks.user_id', $request->input('user'));
        }

        // Filter by category
        if ($request->filled('category')) {
            $tasks->where('category', $request->input('category'));
        }

        // Filter by template
        if ($request->filled('template')) {
            $tasks->where('template_id', $request->input('template'));
        }

        // Filter by overdue reason
        if ($request->filled('reason')) {
            if ($request->input('reason') === 'no_reason') {
                $tasks->with(['client', 'user','overdueReason.overdueReason', 'subtasks', 'activeSubtasks', 'template'])
                    ->leftJoin('task_overdue_reasons', function($join)
                    {
                        $join->on('tasks.id', '=', 'task_overdue_reasons.task_id');
                    })
                    ->whereNull('tasks.completed_at')
                    ->whereNull('task_overdue_reasons.id')
                    ->where('tasks.deadline', '<', Carbon::now());
            } else {
                $tasks->where('tor.reason_id', $request->input('reason'));
            }
        }

        // Filter by from date
        if ($request->filled('from')) {
            $tasks->where('deadline', '>=' ,$request->input('from'));
        }

        // Filter by to date
        if ($request->filled('to')) {
            $tasks->where('deadline', '<=' ,$request->input('to'));
        }

        // Get completed, delivered and paid options
        $paid = $delivered = $completed = ['Yes', 'No'];

        // Get the avalible overdue reasons
        $overdues = OverdueReason::orderBy('priority')
            ->where('complete_task', 0)
            ->where('active', 1)
            ->get();

        // Get avalible users
        $users = Client::active()
            ->whereIn('id', $clients)
            ->with('employee')
            ->has('employee')
            ->groupBy('employee_id')
            ->select('employee_id')
            ->get()
            ->pluck('employee');

        // Get the available categories
        $categories = Task::active()
            ->whereIn('client_id', $clients)
            ->groupBy('category')
            ->select('category')
            ->get()
            ->pluck('category');
        
        // Get the available templates
        $templates = Task::active()
            ->whereIn('client_id', $clients)
            ->with('template')
            ->has('template')
            ->groupBy('template_id')
            ->select('template_id')
            ->get()
            ->pluck('template');

        // Get the available clients
        $clients = $request->user()->getAccessibleClientsQuery()->get();

        // Generate the page path with filter parameters
        $path = url()->current() .
            '?completed=' . $request->input('completed') .
            '&delivered=' . $request->input('delivered') .
            '&paid=' . $request->input('paid') .
            '&user=' . $request->input('user') .
            '&client=' . $request->input('client') .
            '&category=' . $request->input('category') .
            '&template=' . $request->input('template') .
            '&reason=' . $request->input('reason') .
            '&from=' . $request->input('from') .
            '&to=' . $request->input('to');

        return view('dashboard.tasks')->with([
            'completed' => $completed,
            'selectedCompleted' => $request->input('completed', 'No'),
            'delivered' => $delivered,
            'selectedDelivered' => $request->input('delivered'),
            'paid' => $paid,
            'selectedPaid' => $request->input('paid'),
            'users' => $users,
            'selectedUser' => $request->input('user'),
            'clients' => $clients,
            'selectedClient' => $request->input('client'),
            'categories' => $categories,
            'selectedCategory' => $request->input('category'),
            'templates' => $templates,
            'selectedTemplate' => $request->input('template'),
            'overdues' => $overdues,
            'selectedOverdue' => $request->input('reason'),
            'selectedFromDate' => $request->input('from'),
            'selectedToDate' => $request->input('to'),
            'taskcount' => $tasks->count(),
            'tasks' => $tasks->paginate(25)->withPath($path),
        ]);
    }

    /**
     * Filter a task query based on the user-filled fields
     */
    protected function filter($query)
    {
        $category = request()->get('category');
        $client = request()->get('client');

        // Filter by category
        if ($category) {
            $query->where('category', $category);
        }

        // Filter by client
        if ($client) {
            $query->where('client_id', $client);
        }

        // Return the query
        return $query;
    }
}
