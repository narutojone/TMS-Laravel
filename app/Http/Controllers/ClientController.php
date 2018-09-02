<?php

namespace App\Http\Controllers;

use App\Repositories\Client\ClientCreateRequest;
use App\Repositories\Client\ClientInterface;
use App\Repositories\Client\ClientUpdateRequest;
use App\Repositories\Task\TaskInterface;
use App\Repositories\ClientEmployeeLog\ClientEmployeeLog;
use App\Repositories\Contact\Contact;
use App\Repositories\Contact\ContactInterface;
use App\Repositories\Contract\Contract;
use App\Repositories\FolderTemplate\FolderTemplate;
use App\Repositories\System\System;
use App\Repositories\Template\Template;
use App\Repositories\Task\Task;
use App\Repositories\Group\Group;
use App\Repositories\User\UserInterface;
use Carbon\Carbon;
use Huddle\Zendesk\Facades\Zendesk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Repositories\Client\Client;
use App\Repositories\User\User;

class ClientController extends Controller
{
    /**
     * @var $clientRepository - EloquentRepositoryClient
     */
    private $clientRepository;

    /**
     * @var $contactRepository - EloquentRepositoryContact
     */
    private $contactRepository;

    /**
     * @var $userRepository - EloquentRepositoryUser
     */
    private $userRepository;

    /**
     * Instantiate a new controller instance.
     *
     * @param ClientInterface $clientRepository
     * @param ContactInterface $contactRepository
     */
    public function __construct(ClientInterface $clientRepository, ContactInterface $contactRepository, UserInterface $userRepository)
    {
        parent::__construct();

        $this->clientRepository = $clientRepository;
        $this->contactRepository = $contactRepository;
        $this->userRepository = $userRepository;
    }


    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @param null $type
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $type = null)
    {
        if (is_null($type)) {
            // Get the clients the user has access to
            if ($request->user()->hasRole(User::ROLE_ADMIN) || $request->user()->hasRole(User::ROLE_CUSTOMER_SERVICE)) {
                $clients = Client::active()->internal(false)->where('paused', 0);
            } else {
                $clients = $request->user()->getAccessibleClientsQuery()->where('internal', 0)->where('paused', 0);
            }
        } elseif ($type == 'old') {
            $clients = $request->user()->getOldClientsQuery(false)->where('internal', 0)->where('paused', 0);
        } elseif ($type == 'paused') {
            if ($request->user()->hasRole(User::ROLE_EMPLOYEE)) {
                return back()->with('error', "You've not been authorized to visit resource");
            }
            $clients = Client::where('paused', 1)->where('active', 1);
        } elseif ($type == 'deactivated') {
            if ($request->user()->hasRole(User::ROLE_EMPLOYEE)) {
                return back()->with('error', "You've not been authorized to visit resource");
            }
            $clients = Client::where('active', 0);
        } elseif ($type == 'all') {
            if ($request->user()->hasRole(User::ROLE_EMPLOYEE)) {
                return back()->with('error', "You've not been authorized to visit resource");
            }
            $clients = Client::where('internal', 0);
        } elseif ($type == 'internal') {
            if ($request->user()->hasRole(User::ROLE_ADMIN)) {
                $clients = Client::active()->internal();
            } else {
                $clients = $request->user()->getAccessibleClientsQuery()->where('internal', 1);
            }
        }

        // Order results if this is not an API request
        if (! $request->wantsJson()) {
            switch ($request->input('order', 1)) {
                case 1:
                    $clients->orderBy('name', 'ASC'); break;
                case 2:
                    $clients->orderBy('name', 'DESC'); break;
                case 3:
                    $clients->orderBy('risk', 'DESC')->orderBy('name', 'ASC'); break;
                case 4:
                    $clients->orderBy('risk', 'ASC')->orderBy('name', 'ASC'); break;
                case 5:
                    $clients->orderBy('paid', 'ASC')->orderBy('name', 'ASC'); break;
                case 6:
                    $clients->orderBy('paid', 'DESC')->orderBy('name', 'ASC'); break;
                case 7:
                    $clients->orderBy('complaint_case', 'DESC')->orderBy('name', 'ASC'); break;
                case 8:
                    $clients->orderBy('complaint_case', 'ASC')->orderBy('name', 'ASC'); break;
            }
        }

        // Filter by search term (only name)
        if ($request->filled('search')) {
            $clients->where('name', 'LIKE', '%' . $request->input('search') . '%')
                    ->orwhere('organization_number', 'LIKE', '%' . $request->input('search') . '%');
        }

        // Filter by manager
        if ($request->filled('manager')) {
            $clients->where('manager_id', $request->input('manager'));
        }

        // Generate the page path with filter parameters
        $path = url()->current() . '?search=' . $request->input('search') . '&order=' . $request->input('order') . '&manager='.$request->input('manager');

        // Paginate the clients
        $clients = $clients->paginate(25)->withPath($path);

        // Get the list with all available managers
        $managers = User::whereIn('id', Client::active()->whereNotNull('manager_id')->groupBy('manager_id')->pluck('manager_id'))->pluck('name', 'id');

        // Return the raw data for the API
        if ($request->wantsJson()) {
            return $clients;
        }

        // Return the view with the data for the web-app
        return view('clients.index')->with([
            'currentSearch' => $request->input('search'),
            'orderOptions' => [
                1=>'Name ASC',
                2=>'Name DESC',
                3=>'High risk first',
                4=>'High risk last',
                5=>'Not paid first',
                6=>'Not paid last',
                7=>'With complaint first',
                8=>'With complaint last',
            ],
            'managers' => $managers,
            'clients' => $clients,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $contactsRepository = app()->make(ContactInterface::class);
        $contacts = $contactsRepository->model()->orderBy('name', 'ASC')->get();

        return view('clients.create')->with([
            'contacts' => $contacts,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ClientCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClientCreateRequest $request)
    {
        // Authorize request
        $this->authorize('create', Client::class);

        $client = $this->clientRepository->create($request->all());

        return redirect()
            ->action('ClientController@show', $client)
            ->with('success', 'Client created.');
    }

    public function showContacts(Request $request, Client $client)
    {
        return view('clients.contacts.index')->with([
            'client' => $client,
        ]);
    }

    /**
     * Show the form for linking a contact to a client
     *
     * @param Request $request
     * @param Client $client
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function linkContact(Request $request, Client $client)
    {
        // Authorize request
        $this->authorize('update', $client);

        $contacts = $this->contactRepository->model()->get(['name','id']);

        return view('clients.contacts.assign')->with([
            'client'   => $client,
            'contacts' => $contacts,
        ]);
    }

    /**
     * Link an existing contact to a client
     *
     * @param Request $request
     * @param Client $client
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeLinkContact(Request $request, Client $client)
    {
        // Authorize request
        $this->authorize('update', $client);

        $request->validate([
            'contact_id' => 'required|numeric|exists:contacts,id',
            'primary'    => 'required|boolean',
        ]);

        $this->clientRepository->linkContact($client->id, $request->all());

        return redirect()->action('ClientController@showContacts', $client)
            ->with('success', 'Contact added.');
    }

    public function unlinkContact(Request $request, Client $client, Contact $contact)
    {
        // Authorize request
        $this->authorize('update', $client);

        $this->clientRepository->unlinkContact($client->id, $contact->id);
        return redirect()->back()
            ->with('success', 'Client contact removed.');
    }

    /**
     * Show the edit form for a client contact
     *
     * @param Request $request
     * @param Client $client
     * @param Contact $contact
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editContact(Request $request, Client $client, Contact $contact)
    {
        // Authorize request
        $this->authorize('update', $contact);

        return view('clients.contacts.edit')->with([
            'contact' => $contact,
            'client'  => $client,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Repositories\Client\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Client $client)
    {
        if ($request->wantsJson()) {
            return $client;
        }

        $tasks = $client->tasks(false)->with('user')->uncompleted()->orderBy('due_at', 'ASC')->prioritized()->filterPrivate();

        $notifications = $client->notifications()->orderBy('created_at', 'DESC')->limit(5)->get();
        $flags = $client->flags()->withTrashed()->orderBy('pivot_created_at', 'DESC')->limit(5)->get();

        $managerLogs  = $client->employeeLogs(ClientEmployeeLog::TYPE_MANAGER)->paginate(25, ['*'], 'logs_page');
        $employeeLogs = $client->employeeLogs(ClientEmployeeLog::TYPE_EMPLOYEE)->paginate(25, ['*'], 'logs_page');

        return view('clients.show', compact('client') + [
            'mainFolders'   => FolderTemplate::main()->get(),
            'tasks'         => $tasks->paginate(25),
            'notes'         => $client->notes()->with('user')->latest(),
            'notifications' => $notifications,
            'flags'         => $flags,
            'managerLogs'   => $managerLogs,
            'employeeLogs'  => $employeeLogs,
            'riskStatuses' => ['0'=>'Normal', '1'=>'High']
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Repositories\Client\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function edit(Client $client)
    {
        if (! $client->active && ! (auth()->user()->hasRole(User::ROLE_ADMIN) || auth()->user()->hasRole(User::ROLE_CUSTOMER_SERVICE))) {
            return back()->with('error', 'Can\'t edit. Client is deactivated!');
        }

        // The list of active users is needed for selecting the manager and employee
        $activeUsers = $this->userRepository->model()->active()->orderBy('name')->get();

        // Last manager and employee are needed to set a default values on manager and employee selects
        $lastEmployeeId = $client->latestEmployee ? $client->latestEmployee->user_id : $client->employee_id;
        $lastManagerId  = $client->latestManager ? $client->latestManager->user_id : $client->manager_id;

        return view('clients.edit')->with([
            'client'         => $client,
            'activeUsers'    => $activeUsers,
            'lastEmployeeId' => $lastEmployeeId,
            'lastManagerId'  => $lastManagerId,
        ]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Repositories\Client\Client $client
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateNote(Request $request, Client $client)
    {
        $this->validate($request, [
            'note' => 'required',
        ]);

        $client->notes()->create([
            'user_id' => $request->user()->id,
            'note' => $request->input('note'),
        ]);

        return redirect()
            ->action('ClientController@show', $client)
            ->with('success', 'Note added.');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Repositories\Client\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function showNotes(Request $request, Client $client)
    {
        return view('clients.notes')
            ->withClient($client)
            ->withNotes(
                $client->notes()
                    ->with('user')
                    ->orderBy('created_at', 'DESC')
                    ->paginate(25));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Repositories\Client\Client $client
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateRisk(Request $request, Client $client)
    {
        // Everybody can change status from 'normal' to 'high'. Only admin users or PM can switch it back
        if ($client->risk == 1) {
            if (! Auth::user()->hasRole(User::ROLE_ADMIN) && ! Auth::user()->hasRole(User::ROLE_CUSTOMER_SERVICE) && ($client->manager_id != Auth::user()->id)) {
                abort(403, 'Unauthorized');
            }
        }

        $newClientRisk = $request->input('risk', $client->risk);
        $currentClientRisk = $client->risk;

        $this->clientRepository->updateRisk(
            $client,
            $newClientRisk, 
            ($newClientRisk) ? $request->input('risk_reason', $client->risk_reason) : ''
        );
        
        return redirect()
            ->action('ClientController@show', $client)
            ->with('success', 'Risk updated.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ClientUpdateRequest  $request
     * @param  Client  $client
     * @return \Illuminate\Http\Response
     */
    public function update(ClientUpdateRequest $request, Client $client)
    {
        // Authorize request
        $this->authorize('update', $client);
        $client = $this->clientRepository->update($client->id, $request->all());

        return redirect()
            ->action('ClientController@show', $client)
            ->with('success', 'Client updated.');
    }

    /**
     * Display the client's completed tasks.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Repositories\Client\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function completed(Request $request, Client $client)
    {
        if ($request->wantsJson()) {
            return $client->tasks()->completed()->filterPrivate()->paginate(25);
        }

        return view('clients.completed', [
            'client' => $client,
            'tasks' => $client->tasks(false)->completed()->filterPrivate()->latest('completed_at')->with('user')->paginate(25)
        ]);
    }

    /**
     * Add complaint for client
     *
     * @param  \App\Repositories\Client\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function addComplaint(Client $client)
    {
        // Check if client is active
        if (! $client->active) {
            return back()->with('error', 'Complaint can\'t be added. Client is deactivated!');
        }

        // Set complaint case to true
        $client->complaint_case = true;
        $client->save();

        // Find the user in the correct group
        // Group: Kundeservice - Klagesak
        $user = Group::find(9)->users()->inRandomOrder()->first();

        // Make task interface
        $taskRepository = app()->make(TaskInterface::class);

        // Create the task with 7 days from today
        $task = $taskRepository->create([
            'user_id'       => $user->id,
            'client_id'     => $client->id,
            'template_id'   => 48,
            'repeating'     => Task::NOT_REPEATING,
            'deadline'      => Carbon::now()->addDays(7)->format('Y-m-d H:i:s'),
        ]);

        // Return back to client view
        return redirect()
            ->action('ClientController@show', $client);
    }

    /**
     * Remove complaint from client
     *
     * @param  \App\Repositories\Client\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function removeComplaint(Client $client)
    {
        if (! $client->active) {
            return back()->with('error', 'Complaint can\'t be removed. Client is deactivated!');
        }

        $client->complaint_case = false;
        $client->save();

        return redirect()
            ->action('ClientController@show', $client)
            ->with('success', 'Complaint removed.');
    }

    /**
     * List all zendesk tickets assigned to a specific client
     * @param Client $client
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showTickets(Client $client)
    {
        $zendeskData = $this->getOrganizationTickets($client->zendesk_id);

        if(empty($zendeskData)){
            return redirect()->back()->with('error', 'Something went wrong while connecting to Zendesk');
        }
        
        return view('clients.tickets', [
            'client'   => $client,
            'tickets'  => $zendeskData['tickets'],
            'asignees' => $zendeskData['users'],
        ]);
    }

    /**
     * Fetch zendesk ticket comments
     * @param Client $client
     * @param $zendeskTicketId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewTicket(Client $client, $zendeskTicketId )
    {
        $zendeskData = $this->getTicketComments($zendeskTicketId);
        return view('clients.ticket-show', [
            'client'   => $client,
            'ticketid' => $zendeskTicketId,
            'comments' => $zendeskData['comments'],
            'asignees' => $zendeskData['authors'],
        ]);
    }

    private function reassignClientTasks(Client $client, $oldEmployeeId)
    {
        // Move tasks from the former employee to the new
        // also assign any unassigned tasks to the new employee.
        // This is only done for tasks the new employee is able to do.
        // If the new employee cannot do the task, it becomes unassigned.
        $client->load('employee');

        $tasks = $client->tasks(false)->with('template')->uncompleted()->where(function ($query) use ($oldEmployeeId) {
            $query->where('user_id', $oldEmployeeId)->orWhereNull('user_id');
        })->get();

        foreach ($tasks as $task) {

            // Default assignee is null
            $newAssignee = null;
            
            if ($task->template) {
                if($client->employee->canProcessTemplate($task->template)) {
                    $newAssignee = $client->employee->id;
                }
            } else {
                // Custom task
                $newAssignee = $client->employee->id;
            }

            // Update task user
            $task->user_id = $newAssignee;
            $task->save();

            // Update all uncompleted subtasks for this particular task
            $task->subtasks()->whereNull('completed_at')->update([
                'user_id' => $newAssignee,
            ]);
        }
    }

    private function getOrganizationTickets($organizationNumber = null)
    {
        if(is_null($organizationNumber) || trim($organizationNumber) == '') {
            return ['tickets' => [], 'users' => []];
        }
        $tickets = $asignees = [];

        $page = 1;
        try {
            do {
                $response = Zendesk::tickets()->findAll([
                    'page' => $page,
                    'organization_id' => $organizationNumber,
                    'sort_by'=>'created_at',
                    'sort_order'=>'desc'
                ]);
                foreach ($response->tickets as $ticket) {
                    $tickets[] = [
                        'id'       => $ticket->id,
                        'name'     => $ticket->subject,
                        'status'   => $ticket->status,
                        'assignee' => $ticket->assignee_id,
                    ];
                    $asignees[] = $ticket->assignee_id;
                }
                $page++;
                $nextPage = $response->next_page;
            } while (!is_null($nextPage));
        }
        catch(ApiResponseException $e) {
            return [];
        }

        $users = DB::table('zendesk_users')->whereIn('zendesk_id', $asignees)->get();
        $asignees = [];

        foreach($users as $user) {
            $asignees[$user->zendesk_id] = $user->name;
        }

        return ['tickets' => $tickets, 'users'=>$asignees];
    }

    private function getTicketComments($ticketId)
    {
        if(is_null($ticketId) || trim($ticketId) == '') return [];
        $comments = $authors = [];
        $page = 1;

        try {
            do {
                $response = Zendesk::tickets($ticketId)->comments()->findAll([
                    'page'      => $page,
                    'sort_by'   => 'created_at',
                    'sort_order'=> 'desc'
                ]);
                foreach ($response->comments as $comment) {
                    $comments[] = [
                        'id'         => $comment->id,
                        'created_at' => $comment->created_at,
                        'content'    => $comment->html_body,
                        'author'     => $comment->author_id,
                        'public'     => (bool)$comment->public
                    ];
                    $authors[] = $comment->author_id;
                }
                $page++;
                $nextPage = $response->next_page;
            } while (!is_null($nextPage));
        }
        catch(ApiResponseException $e) {
            return [];
        }

        $users = DB::table('zendesk_users')->whereIn('zendesk_id', $authors)->get();

        $authors = [];
        foreach($users as $user) {
            $authors[$user->zendesk_id] = $user->name;
        }

        return ['comments' => $comments, 'authors' => $authors];
    }

    /**
     * List of client notifications
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Repositories\Client\Client  $client
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showNotifications(Request $request, Client $client)
    {
        $this->authorize('view', $client);

        $notifications = $client->notifications()->orderBy('created_at', 'DESC')->paginate(25);
        return view('clients.notifications', [
            'client'        => $client,
            'notifications' => $notifications,
        ]);
    }

    /**
     * List of client flags
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Repositories\Client\Client  $client
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showFlags(Request $request, Client $client)
    {
        $this->authorize('view', $client);
        
        $flags = $client->flags()->withTrashed()->orderBy('pivot_created_at', 'DESC')->paginate(25);
        return view('clients.flags', [
            'client' => $client,
            'flags'  => $flags,
        ]);
    }

}
