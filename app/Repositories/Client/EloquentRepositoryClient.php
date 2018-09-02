<?php
 
namespace App\Repositories\Client;

use App\Repositories\ClientEditLog\ClientEditLogInterface;
use Auth;
use App\Repositories\BaseEloquentRepository;
use App\Repositories\ClientEmployeeLog\ClientEmployeeLog;
use App\Repositories\Contact\Contact;
use App\Repositories\Contact\ContactInterface;
use App\Repositories\Contract\Contract;
use App\Repositories\Option\OptionInterface;
use App\Repositories\Task\TaskInterface;
use App\Repositories\ProcessedNotification\ProcessedNotification;
use App\Repositories\ProcessedNotification\ProcessedNotificationInterface;
use App\Repositories\System\System;
use App\Repositories\System\SystemInterface;
use App\Repositories\TemplateNotification\TemplateNotification;
use App\Repositories\TemplateNotification\TemplateNotificationInterface;
use App\Repositories\User\User;
use App\Repositories\User\UserInterface;
use App\Repositories\ZendeskGroup\ZendeskGroupInterface;
use Carbon\Carbon;
use Huddle\Zendesk\Facades\Zendesk;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Ixudra\Curl\Facades\Curl;
use App\Repositories\GroupUser\GroupUserInterface;
use App\Repositories\Task\Task;

/**
 * The eloquent element in a repository should contain all data manipulation related to a entity
 */
class EloquentRepositoryClient extends BaseEloquentRepository implements ClientInterface
{
    /**
     * @var $model
     */
    protected $model;

    protected $processedNotificationRepo;

    protected $clientEditLogRepository;

    protected $taskRepository;

    protected $optionRepository;

    protected $groupUserRepository;

    /**
     * Attributes that can only be updated by admin
     */
    protected $protectedAttributes = [
        'manager_id',
        'employee_id',
        'paid',
        'active',
        'paused',
    ];

    /**
     * EloquentRepositoryClient constructor.
     *
     * @param Client $model
     */
    public function __construct(Client $model)
    {
        parent::__construct();

        $this->model = $model;
        $this->processedNotificationRepo = app()->make(ProcessedNotificationInterface::class);
        $this->clientEditLogRepository = app()->make(ClientEditLogInterface::class);
        $this->taskRepository = app()->make(TaskInterface::class);
        $this->optionRepository = app()->make(OptionInterface::class);
        $this->groupUserRepository = app()->make(GroupUserInterface::class);
    }

    /**
     * Create a new Client.
     *
     * @param array $input
     *
     * @return Client
     * @throws ValidationException
     */
    public function create(array $input)
    {
        $input = $this->prepareCreateData($input);

        if(!$this->isValid('create', $input)) {
            throw new ValidationException($this->validators['create']);
        }
        $client = $this->model->create($input);

        // Only create contact if it's not internal
        if($input['internal'] == Client::NOT_INTERNAL){
            if($input['contact_type'] == 'new') {
                // Create a new contact and link it to the client
                $contactRepository = app()->make(ContactInterface::class);
                $contactRepository->create([
                    'client_id' => $client->id,
                    'name'      => $input['contact_name'],
                    'address'   => $input['contact_email'],
                    'number'    => $input['contact_phone'],
                    'primary'   => 1,
                ]);
            }
            else {
                // Link en existing contact to the client
                $this->linkContact($client->id, [
                    'primary'    => 1,
                    'contact_id' => $input['contact_id'],
                ]);
            }
        }

        // Create ZenDesk Organization (only if client is NOT internal)
        if($client->internal == $this->model::NOT_INTERNAL) {
            // Get client extra details
            $apiDetails = $this->getClientDetails($client->organization_number);
            $client->update($apiDetails);

            // Update Zendesk
            $this->createZenDeskOrganization($client);
        }

        $this->updateClientEditLog($client->id, 'active', $client->active);
        $this->updateClientEditLog($client->id, 'paid', $client->paid);
        $this->updateClientEditLog($client->id, 'paused', $client->paused);

        return $client;
    }

    /**
     * Update a Client.
     *
     * @param integer $id
     * @param array $input
     *
     * @return Client
     * @throws ValidationException
     * @throws ModelNotFoundException
     */
    public function update($id, array $input)
    {
        $input = $this->prepareUpdateData($id, $input);

        if(!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }

        $client = $this->find($id);
        if ($client) {
            // Save a copy of client for later comparison
            $initialClient = $client->replicate();
            $client->fill($input);
            $client->save();

            // Update status of all related tasks if changed paid status
            if(isset($input['paid']) && ($input['paid'] != $initialClient->paid)) {
                $tasks = $client->tasks(!$input['paid'])->uncompleted();
                $tasks->update(['active' => $input['paid']]);

                if ($input['paid'] == Client::IS_PAID) {
                    foreach ($tasks->get() as $task) {
                        if ($task->isOverdue()) {
                            $this->addTaskOverdueReason($task, 'overdue_reason_client_paid', 'The task was overdue when the client was marked as paid.');
                        }
                    }
                } else {
                    $this->createNotPaidTask($client);
                }
            }

            // Process the change of the employee
            if ($initialClient->employee_id != $client->employee_id) {
                $this->reassignClientTasks($client, $initialClient->employee_id);
                // Update employee logs
                $this->updateEmployeeLogs($client, $initialClient, $input, ClientEmployeeLog::TYPE_EMPLOYEE);
            }

            // Process the change of the manager
            if ($initialClient->manager_id != $client->manager_id) {
                // Update manager logs
                $this->updateEmployeeLogs($client, $initialClient, $input, ClientEmployeeLog::TYPE_MANAGER);
            }

            // Check if the client is not internal
            if($client->internal == $this->model::NOT_INTERNAL){
                // Send notifications based on client changes
                $this->sendNotifications($client, $initialClient);

                // Check if the client has an organization number
                if($client->organization_number){
                    // Update client information from BRREG API
                    $apiDetails = $this->getClientDetails($client->organization_number);
                    $client->update($apiDetails);

                    // Update Zendesk Organization
                    if($client->zendesk_id){
                        $this->updateZenDeskOrganization($client, $initialClient);
                    } else {
                         $this->createZenDeskOrganization($client);
                    }
                }   
            }
            
            // Process the change of paused status
            if(isset($input['paused']) && ($input['paused'] != $initialClient->paused)) {
                $tasks = $client->tasks($input['paused'])->uncompleted();
                $tasks->update(['active' => !$input['paused']]);

                if ($input['paused'] == Client::IS_PAUSED) {
                    $this->createPausedTask($client);
                }

                if ($input['paused'] == 0) {
                    foreach ($tasks->get() as $task) {
                        if ($task->isOverdue()) {
                            $this->addTaskOverdueReason($task, 'overdue_reason_client_unpaused', 'The task was overdue when the client was unpaused.');
                        }
                    }
                }
            }

            // Log changes to 'active', 'paused' and 'paid' fields
            $this->saveLogs($client, $initialClient, $input);

            return $client;
        }

        throw new ModelNotFoundException('Model Client not found', 404);
    }

    /**
     * @param Client $client
     * @param Client $initialClient
     * @param array $input
     */
    private function saveLogs(Client $client, Client $initialClient, array $input)
    {
        if ($client->active != $initialClient->active) {
            $this->updateClientEditLog($client->id, 'active', $client->active, $input['active_comment']);
        }

        if ($client->paused != $initialClient->paused) {
            $this->updateClientEditLog($client->id, 'paused', $client->paused, $input['paused_comment']);
        }

        if ($client->paid != $initialClient->paid) {
            $this->updateClientEditLog($client->id, 'paid', $client->paid, $input['paid_comment']);
        }
    }

    /**
     * Remove a contact person from a client (unlink)
     *
     * @param $id
     * @param $contactId
     */
    public function unlinkContact($id, $contactId)
    {
        $client = $this->find($id);
        $client->contacts()->detach([$contactId]);
    }

    /**
     * Add a contact person for a client
     *
     * @param $id
     * @param $input
     * @throws \Exception
     */
    public function linkContact($id, $input)
    {
        $client = $this->find($id);

        DB::beginTransaction();

        try {
            if ((int)$input['primary'] == 1) {
                // Update all other contacts to non primary
                DB::table('client_contact')->where('client_id', $client->id)->update([
                    'primary' => 0,
                ]);
            }

            $client->contacts()->attach([
                $input['contact_id'] => ['primary' => $input['primary']],
            ]);
        }
        catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }

        DB::commit();
    }

    public function updateClientEditLog($clientId, $field, $newValue, $comment = '')
    {
        // Set the end date of the last change log of the field
        $this->clientEditLogRepository->endLatest($clientId, $field);

        $this->clientEditLogRepository->create([
            'field'     => $field,
            'value'     => $newValue,
            'comment'   => $comment,
            'starts_at' => Carbon::now(),
            'client_id' => $clientId,
            'user_id'   => Auth::user() ? Auth::user()->id : null,
        ]);
    }

    protected function sendNotifications(Client $client, Client $initialClient) : void
    {
        $changes = $client->getChanges();
        $optionRepository = app()->make(OptionInterface::class);

        $this->checkForActiveChange($changes, $optionRepository, $client, $initialClient);
        $this->checkForPaidChange($changes, $optionRepository, $client, $initialClient);
        $this->checkForEmployeeChange($changes, $optionRepository, $client, $initialClient);
        $this->checkForManagerChange($changes, $optionRepository, $client, $initialClient);
        $this->checkForPausedChange($changes, $optionRepository, $client, $initialClient);
    }

    protected function checkForManagerChange($changes, $optionRepository, $client, $initialClient)
    {
        $clientEmail = $client->email();
        if(is_null($clientEmail)) {
            return;
        }

        $sendNotification = true;
        // Check if active field was changed or if client is not active
        if (isset($changes['active']) || $client->active == Client::NOT_ACTIVE) {
            $sendNotification = false;
        }
        // Check if paused field was changed or if client is not paused
        elseif (isset($changes['paused']) || $client->paused == Client::IS_PAUSED) {
            $sendNotification = false;
        }

        if (isset($changes['manager_id']) && !is_null($changes['manager_id']) && $sendNotification) {
            //  send "Client new employee email template"
            $emailTemplate = $optionRepository->model()->where('key', '=', 'client_new_manager')->first()->emailTemplate;
            $data = [
                'clientname' => $client->name,
                'employeename' => (isset($client->employee)) ? $client->employee->name : "",
                'managername' => (isset($client->manager)) ? $client->manager->name : "",
                'template_id'   => $emailTemplate->id,
                'employeepf' => 'PF' . ((isset($client->employee)) ? $client->employee->pf_id : ""),
            ];

            foreach ($data as $name => $value) {
                $subject = str_replace("[[{$name}]]", $value, "Oppdragsansvarlig");
            }

            $response = notification('template')
                ->template($emailTemplate->id)
                ->subject($subject)
                ->to($clientEmail)
                ->from('')
                ->data($data)
                ->saveForApproving(null, $client->name, null);
        }
    }

    protected function checkForPausedChange($changes, $optionRepository, $client, $initialClient)
    {
        $clientEmail = $client->email();
        if(is_null($clientEmail)) {
            return;
        }

        if (isset($changes['paused']) && $changes['paused'] == Client::IS_PAUSED) {
            $emailTemplate = $optionRepository->model()->where('key', '=', 'client_paused_email_template')->first()->emailTemplate;

            $data = [
                'clientname' => $client->name,
                'employeename' => (isset($client->employee)) ? $client->employee->name : "",
                'managername' => (isset($client->manager)) ? $client->manager->name : "",
                'template_id'   => $emailTemplate->id,
                'employeepf' => 'PF' . ((isset($client->employee)) ? $client->employee->pf_id : ""),
            ];

            $response = notification('template')
                ->template($emailTemplate->id)
                ->subject($emailTemplate->title)
                ->to($clientEmail)
                ->from('')
                ->data($data)
                ->saveForApproving(null, $client->name, null);
        }

        if (isset($changes['paused']) && $changes['paused'] == Client::NOT_PAUSED) {
            $emailTemplate = $optionRepository->model()->where('key', '=', 'client_not_paused_email_template')->first()->emailTemplate;

            $data = [
                'clientname' => $client->name,
                'employeename' => (isset($client->employee)) ? $client->employee->name : "",
                'managername' => (isset($client->manager)) ? $client->manager->name : "",
                'template_id'   => $emailTemplate->id,
                'employeepf' => 'PF' . ((isset($client->employee)) ? $client->employee->pf_id : ""),
            ];

            $response = notification('template')
                ->template($emailTemplate->id)
                ->subject($emailTemplate->title)
                ->to($clientEmail)
                ->from('')
                ->data($data)
                ->saveForApproving(null, $client->name, null);
        }
    }

    protected function checkForEmployeeChange($changes, $optionRepository, $client, $initialClient)
    {
        $clientEmail = $client->email();
        if(is_null($clientEmail)) {
            return;
        }

        $sendNotification = true;
        // Check if active field was changed or if client is not active
        if (isset($changes['active']) || $client->active == Client::NOT_ACTIVE) {
            $sendNotification = false;
        }
        // Check if paused field was changed or if client is not paused
        elseif (isset($changes['paused']) || $client->paused == Client::IS_PAUSED) {
            $sendNotification = false;
        }

        if (isset($changes['employee_id']) && !is_null($changes['employee_id']) && $sendNotification) {
            //  send "Client new employee email template"
            $emailTemplate = $optionRepository->model()->where('key', '=', 'client_new_employee')->first()->emailTemplate;
            $data = [
                'clientname' => $client->name,
                'employeename' => (isset($client->employee)) ? $client->employee->name : "",
                'managername' => (isset($client->manager)) ? $client->manager->name : "",
                'template_id'   => $emailTemplate->id,
                'employeepf' => 'PF' . ((isset($client->employee)) ? $client->employee->pf_id : ""),
            ];

            foreach ($data as $name => $value) {
                $subject = str_replace("[[{$name}]]", $value, "Ny konsulent");
            }

            $response = notification('template')
                ->template($emailTemplate->id)
                ->subject($subject)
                ->to($clientEmail)
                ->from('')
                ->data($data)
                ->saveForApproving(null, $client->name, null);
        }
    }

    protected function checkForPaidChange($changes, $optionRepository, $client, $initialClient)
    {
        $clientEmail = $client->email();
        if(is_null($clientEmail)) {
            return;
        }

        if (isset($changes['paid']) && $changes['paid'] == Client::IS_PAID) {
            $emailTemplate = $optionRepository->model()->where('key', '=', 'client_paid_email_template')->first()->emailTemplate;

            $data = [
                'clientname' => $client->name,
                'employeename' => (isset($client->employee)) ? $client->employee->name : "",
                'managername' => (isset($client->manager)) ? $client->manager->name : "",
                'template_id'   => $emailTemplate->id,
                'employeepf' => 'PF' . ((isset($client->employee)) ? $client->employee->pf_id : ""),
            ];

            foreach ($data as $name => $value) {
                $subject = str_replace("[[{$name}]]", $value, "Din profil er nå reaktivert");
            }

            $response = notification('template')
                ->template($emailTemplate->id)
                ->subject($subject)
                ->to($clientEmail)
                ->from('')
                ->data($data)
                ->saveForApproving(null, $client->name, null);
        }

        if (isset($changes['paid']) && $changes['paid'] == Client::NOT_PAID) {
            $emailTemplate = $optionRepository->model()->where('key', '=', 'client_not_paid_email_template')->first()->emailTemplate;
            $data = [
                'clientname' => $client->name,
                'employeename' => (isset($client->employee)) ? $client->employee->name : "",
                'managername' => (isset($client->manager)) ? $client->manager->name : "",
                'template_id'   => $emailTemplate->id,
                'employeepf' => 'PF' . ((isset($client->employee)) ? $client->employee->pf_id : ""),
            ];

            foreach ($data as $name => $value) {
                $subject = str_replace("[[{$name}]]", $value, "Din profil er nå deaktivert");
            }

            $response = notification('template')
                ->template($emailTemplate->id)
                ->subject($subject)
                ->to($clientEmail)
                ->from('')
                ->data($data)
                ->saveForApproving(null, $client->name, null);
        }
    }

    protected function checkForActiveChange($changes, $optionRepository, $client, $initialClient)
    {
        $clientEmail = $client->email();
        if(is_null($clientEmail)) {
            return;
        }

        if (isset($changes['active']) && $changes['active'] == Client::NOT_ACTIVE) {
            $emailTemplate = $optionRepository->model()->where('key', '=', 'client_deactivate_email_template')->first()->emailTemplate;
            $data = [
                'clientname' => $client->name,
                'employeename' => (isset($initialClient->employee)) ? $initialClient->employee->name : "",
                'managername' => (isset($initialClient->manager)) ? $initialClient->manager->name : "",
                'template_id'   => $emailTemplate->id,
                'employeepf' => 'PF' . ((isset($client->employee)) ? $client->employee->pf_id : ""),
            ];

            foreach ($data as $name => $value) {
                $subject = str_replace("[[{$name}]]", $value, "Stengt profil");
            }

            $response = notification('template')
                ->template($emailTemplate->id)
                ->subject($subject)
                ->to($clientEmail)
                ->from('')
                ->data($data)
                ->saveForApproving(null, $client->name, null);
        }

    }


    /**
     * Delete a Task.
     *
     * @param integer $id
     *
     * @return void
     */
    public function delete($id)
    {
        $client = $this->model->find($id);
        if (!$client) {
            throw new ModelNotFoundException('Model Client not found', 404);
        }

        $client->delete();
    }

    /**
     * Prepare data for create action.
     *
     * @param array $input
     * @return array
     */
    private function prepareCreateData($input)
    {
        if(!isset($input['active'])) {
            $input['active'] = Client::IS_ACTIVE;
        }
        if(!isset($input['paid'])) {
            $input['paid'] = Client::IS_PAID;
        }
        if(!isset($input['paused'])) {
            $input['paused'] = Client::NOT_PAUSED;
        }
        if(!isset($input['internal'])) {
            $input['internal'] = Client::NOT_INTERNAL;
        }
        if(!isset($input['show_folders'])) {
            $input['show_folders'] = 1;
        }
        if(!isset($input['risk'])) {
            $input['risk'] = 0;
        }
        if(!isset($input['complaint_case'])) {
            $input['complaint_case'] = 0;
        }
        if(!isset($input['risk_reason'])) {
            $input['risk_reason'] = null;
        }
        if(!isset($input['type'])) {
            $input['type'] = Client::TYPE_UNKNOWN;
        }

        if($input['risk'] == 0) {
            $input['risk_reason'] = null;
        }

        if($input['internal'] == Client::IS_INTERNAL) {
            $input['organization_number'] = null;
            $input['type'] = Client::TYPE_UNKNOWN;
        }
        else {
            $clientApiDetails = $this->getClientDetails($input['organization_number']);

            if(empty($clientApiDetails)){
                $input['type'] = Client::TYPE_UNKNOWN;
            } else {
                $input['type'] = $clientApiDetails['type'];
                $input['country_code'] = $clientApiDetails['country_code'];
                $input['city'] = $clientApiDetails['city'];
                $input['address'] = $clientApiDetails['address'];
                $input['postal_code'] = $clientApiDetails['postal_code'];
            }
        }

        return $input;
    }

    /**
     * Prepare data for update action.
     *
     * @param $id
     * @param array $data
     * @return array
     */
    protected function prepareUpdateData($id, array $data)
    {
        // Prevent update of attributes that can only be updated by an admin
        if(!auth()->user()->hasRole(User::ROLE_ADMIN) && !auth()->user()->hasRole(User::ROLE_CUSTOMER_SERVICE)) {
            foreach($this->protectedAttributes as $attribute) {
                unset($data[$attribute]);
            }
        }

        // Remove manager and employee if client not active anymore
        if(isset($data['active']) && $data['active'] == 0) {
            $data['manager_id'] = null;
            $data['employee_id'] = null;
        }

        if (isset($data['paused']) && $data['paused'] == Client::IS_PAUSED) {
            // Get the initial client before update is made
            $initialClient = $this->model->find($id);

            // If the client is moved from unpaused to paused
            if ($data['paused'] != $initialClient->paused) {
                // Get option data + fetch latest edit log data
                $option = $this->optionRepository->model()->where('key', 'client_paused_day_limit')->first();
                $madeUnpaused = $initialClient->editLogs()->where(['field' => 'paused', 'value' => 0, 'ends_at' => null])->first();

                // Deny marking the client as paused if within the time limit
                if ($option && $option->value && $madeUnpaused && Carbon::parse($madeUnpaused->starts_at)->addDays($option->value) > Carbon::now()) {
                    $data['paused'] = Client::NOT_PAUSED;
                    session()->flash('error', 'Client was not marked as paused due to day limitation. Contact the IT for more information.');
                }
            }

            // Remove manager and employee if client is paused
            if ($data['paused'] == Client::IS_PAUSED) {
                $data['manager_id'] = null;
                $data['employee_id'] = null;
            }
        }

        if(!isset($data['active_comment']) || is_null($data['active_comment'])) {
            $data['active_comment'] = '';
        }
        if(!isset($data['paused_comment']) || is_null($data['paused_comment'])) {
            $data['paused_comment'] = '';
        }
        if(!isset($data['paid_comment']) || is_null($data['paid_comment'])) {
            $data['paid_comment'] = '';
        }

        return $data;
    }

    protected function getDefaultClientSystem()
    {
        $systemRepository = app()->make(SystemInterface::class);
        $system = $systemRepository->model()->where('default', System::IS_DEFAULT)->first();
        if (!$system) {
            throw new ModelNotFoundException('Model System not found', 404);
        }

        return $system->id;
    }

    /**
     * Move tasks from the former employee to the new
     * also assign any unassigned tasks to the new employee.
     * This is only done for tasks the new employee is able to do.
     * If the new employee cannot do the task, it becomes unassigned.
     *
     * @param Client $client
     * @param int $oldEmployeeId
     */
    protected function reassignClientTasks(Client $client, ?int $oldEmployeeId)
    {
        if(!$client->active || $client->paused) {
            // Set task and subtask users to null
            foreach ($client->tasks(false)->uncompleted()->get() as $task){
                $task->update(['user_id' => null]);
                $task->subtasks()->whereNull('completed_at')->update(['user_id' => null]);
            }
            
            return;
        }

        $client->load('employee');

        $tasks = $client->tasks(false)->with('template')->uncompleted()->where(function ($query) use ($oldEmployeeId) {
            $query->where('user_id', $oldEmployeeId)->orWhereNull('user_id');
        })->get();

        foreach ($tasks as $task) {
            // TODO(alex) - this logic should be moved into task repository
            // Default assignee is null
            $newAssignee = null;

            if($client->employee) {
                if ($task->template) {
                    if($client->employee->canProcessTemplate($task->template)) {
                        $newAssignee = $client->employee->id;
                    }
                } else {
                    // Custom task
                    $newAssignee = $client->employee->id;
                }
            }

            // Update task user
            $task->user_id = $newAssignee;
            $task->save();

            // Update all uncompleted subtasks for this particular task
            $task->subtasks()->whereNull('completed_at')->update([
                'user_id' => $newAssignee,
            ]);
            
            // Give the task an overdue reason assigned from system if it's overdue
            if($task->isOverdue()){
                $this->addTaskOverdueReason($task, 'overdue_reason_client_move', 'The task was overdue when the client was moved.');
            }
        }
    }

    protected function updateEmployeeLogs(Client $client, Client $copy, array $input, string $type = ClientEmployeeLog::TYPE_EMPLOYEE)
    {
        // TODO(alex) - replace this section with firstOrCreate()
        // Find userId for the old employee or manager
        $userId = ($type == ClientEmployeeLog::TYPE_EMPLOYEE ? $copy->employee_id : $copy->manager_id);

        // Check if log does not have entry from before and add it
        if (! $log = $client->employeeLogs($type)->where('user_id', $userId)->first()) {
            $log = $client->employeeLogs($type)->save(new ClientEmployeeLog([
                'user_id' => $userId,
                'type'    => $type,
            ]));
        }

        // Find the rating based on the type
        $rating_type = ($type == ClientEmployeeLog::TYPE_EMPLOYEE ? 'employee_move_rating' : 'manager_move_rating');

        // Adapt rating
        if(!isset($input[$rating_type]) || $input[$rating_type]==='null') {
            $input[$rating_type] = null;
        }

        // Update rating for old user
        $log->update([
            'rating'     => $input[$rating_type],
            'removed_at' => Carbon::now()
        ]);

        // Create new log for current assigned user
        $client->employeeLogs()->save(new ClientEmployeeLog([
            'user_id'     => ($type == ClientEmployeeLog::TYPE_EMPLOYEE ? $client->employee_id : $client->manager_id),
            'type'        => $type,
            'assigned_at' => Carbon::now(),
        ]));
    }

    protected function createZenDeskOrganization(Client $client)
    {
        // TODO (alex) Place this value in settings
        $zendeskGroupId = 26362145; // Fallback group ID

        // Get ZenDesk group_id based on client's employee PF_ID
        if($client->employee && $client->employee->pf_id != null) {
            $zendeskGroupRepository = app()->make(ZendeskGroupInterface::class);
            $zendeskGroup = $zendeskGroupRepository->model()->where('deleted', 0)->where('name', '=', 'PF' . $client->employee->pf_id)->first();
            if($zendeskGroup) {
                $zendeskGroupId = $zendeskGroup->group_id;
            }
        }

        // Prepare API data to create ZenDesk organization
        $attributes = [
            'name'          => $client->name,
            'external_id'   => $client->organization_number,
            'group_id'      => $zendeskGroupId,
        ];

        //
        try {
            $response = Zendesk::organizations()->create($attributes);

            // Update clients's 'zendesk_id'
            if(isset($response->organization)) {
                $client->update([
                    'zendesk_id' => $response->organization->id,
                ]);
            }
        }
        catch (\Exception $e) {
            // TODO (alex) What shall we do if ZenDesk throws errors on creation ?
        }
    }

    protected function updateZenDeskOrganization(Client $client, Client $initialClient)
    {
        // TODO (alex) Place this value in settings
        $zendeskGroupId = 26362145; // Fallback group ID

        // Get fresh data for the client
        $client = $client->fresh();

        // Get ZenDesk group_id based on client's employee PF_ID
        if($client->active == Client::IS_ACTIVE && $client->employee && $client->employee->pf_id != null) {
            $zendeskGroupRepository = app()->make(ZendeskGroupInterface::class);
            $zendeskGroup = $zendeskGroupRepository->model()->where('deleted', 0)->where('name', '=', 'PF' . $client->employee->pf_id)->first();
            if($zendeskGroup) {
                $zendeskGroupId = $zendeskGroup->group_id;
            }
        }

        $zenDeskAttributesToUpdate = [
            'name'        => $client->name,
            'external_id' => $client->organization_number,
            'group_id'    => $zendeskGroupId,
        ];

        try {
            $response = Zendesk::organizations()->update($client->zendesk_id, $zenDeskAttributesToUpdate);
        } catch(\Exception $e) {
            // TODO (alex) What shall we do if ZenDesk throws errors on update ?
        }

        // Update ZenDesk organisation tickets (only if employee is changed)
        if($client->employee_id != $initialClient->employee_id && !is_null($client->employee_id)) {
            $data = [];
            $ticketsToBeUpdated = [];
            $userRepository = app()->make(UserInterface::class);
            $zendeskGroupRepository = app()->make(ZendeskGroupInterface::class);

            $organizationTickets = $this->getOrganizationTickets($client->zendesk_id);
            $oldEmployee = $userRepository->model()->where('id', $initialClient->employee_id)->first();

            if($oldEmployee){
                $oldZenDeskGroup = $zendeskGroupRepository->model()->where('deleted',0)->where('name','=', 'PF'.$oldEmployee->pf_id)->first();

                // Check if we have an old group, if not skip
                if($oldZenDeskGroup){
                    foreach($organizationTickets as $ticket)
                    {
                        if(trim($ticket['groupId']) == trim($oldZenDeskGroup->group_id)) {
                            $ticketsToBeUpdated[] = [
                                'zendeskTicketId'   => $ticket['id'],
                                'groupId'           => $zendeskGroupId,
                            ];
                        }
                    }                    

                    $data['commentBody'] = "The client and tickets have been moved from {$oldEmployee->name} to {$client->employee->name}.";
                    $this->updateTickets($ticketsToBeUpdated, $data);
                }
            }
        }
    }

    protected function getOrganizationTickets($organizationNumber = null)
    {
        if(is_null($organizationNumber) || trim($organizationNumber) == '') return [];
        $tickets = [];
        $page = 1;

        try {
            do {
                $response = Zendesk::tickets()->findAll(['page' => $page, 'organization_id' => $organizationNumber]);
                foreach ($response->tickets as $ticket) {
                    $tickets[] = ['groupId'=>$ticket->group_id, 'id'=>$ticket->id];
                }
                $page++;
                $nextPage = $response->next_page;
            } while (!is_null($nextPage));
        }
        catch(\Exception $e) {
            return [];
        }

        return $tickets;
    }

    protected function updateTickets($tickets, $data = array())
    {
        if(!is_array($tickets) || empty($tickets)) return;
        $newGroupId = $tickets[0]['groupId'];

        $ticketsChunks = array_chunk($tickets, 100);
        foreach($ticketsChunks as $chunk)
        {
            $ticketIds = [];
            foreach($chunk as $ticket)
            {
                $ticketIds[] = $ticket['zendeskTicketId'];
            }

            $dataToBeUpdated = [
                'ids'      => $ticketIds,
                'group_id' => $newGroupId
            ];

            // add comments (if any)
            if(!empty($data)) {
                if(isset($data['commentBody']) && trim($data['commentBody'])!='') {
                    $dataToBeUpdated['comment'] = [
                        'body'      => trim($data['commentBody']),
                        "public"    => false,
                    ];
                }
            }

            try {
                $response = Zendesk::tickets()->updateMany($dataToBeUpdated);
            }
            catch(\Exception $e) {
                // TODO (alex) What shall we do if ZenDesk throws errors on update tickets ?
            }
        }
    }

    /**
     * Get client extra details (type+address) from http://data.brreg.no based on organization number
     *
     * @param string $organizationNumber
     * @return array
     */
    public function getClientDetails(string $organizationNumber) : array
    {
        if(trim($organizationNumber) == '') {
            return [];
        }

        $url = "http://data.brreg.no/enhetsregisteret/enhet/{$organizationNumber}.json";
        $clientTypeMapping = [
            'as'  => Client::TYPE_AS,
            'enk' => Client::TYPE_ENK,
            'da'  => Client::TYPE_AS,
            'nuf' => Client::TYPE_AS,
            'fli' => Client::TYPE_AS,
            'ans' => Client::TYPE_AS,
        ];
        $dataToBeUpdated = [];

        $apiReponse = Curl::to($url)->returnResponseObject()->get();
        $clientDetails = json_decode($apiReponse->content);

        // Fetch type
        if(isset($clientDetails->orgform) && isset($clientDetails->orgform->kode)) {
            $apiOrgForm = strtolower($clientDetails->orgform->kode);
            if(isset($clientTypeMapping[$apiOrgForm])) {
                $dataToBeUpdated['type'] = $clientTypeMapping[$apiOrgForm];
            }
            else {
                $dataToBeUpdated['type'] = Client::TYPE_UNKNOWN;
            }
        }

        // Fetch name
        if(isset($clientDetails->navn)) {
            $dataToBeUpdated['name'] = trim($clientDetails->navn);
        }

        // Fetch address
        $address = null;
        if(isset($clientDetails->postadresse)) {
            $address = $clientDetails->postadresse;
        }
        elseif(isset($clientDetails->forretningsadresse)) {
            $address = $clientDetails->forretningsadresse;
        }

        if($address !== null) {
            $dataToBeUpdated += [
                'country_code' => isset($address->landkode) ? $address->landkode : '',
                'city'         => isset($address->kommune) ? $address->kommune : '',
                'address'      => isset($address->adresse) ? $address->adresse : '',
                'postal_code'  => isset($address->postnummer) ? $address->postnummer : '',
            ];
        }

        return $dataToBeUpdated;
    }

    /**
     * Update client risk
     *
     * @param \App\Repositories\Client\Client $client
     * @param int $risk
     * @param string $reason
     * @param string $comment
     * @return void
     */
    public function updateRisk(Client $client, $risk, $reason, $comment = '')
    {
        $currentClientRisk = $client->risk;

        $client->update([
            'risk' => $risk,
            'risk_reason' => $reason
        ]);

        if ($currentClientRisk != $risk) {
            $this->updateClientEditLog($client->id, 'risk', $client->risk, $comment);

            if ($risk == Client::IS_HIGH_RISK) {
                $this->createHighRiskTask($client);
            }
        }
    }

    /**
     * Create task when client set as paused
     *
     * @param \App\Repositories\Client\Client $client
     * @return boolean
     */
    protected function createPausedTask(Client $client)
    {
        // Find template for task on paused client
        $optionRepository = app()->make(OptionInterface::class);
        $template = $optionRepository->model()->where('key', '=', 'client_paused_template')->first()->template;
        if (!$template) {
            return false;
        }

        // Find user from group
        $group = $optionRepository->model()->where('key', '=', 'client_paused_user_group')->first()->group;
        $userId = null;
        if ($group) {
            $groupUserRepository = app()->make(GroupUserInterface::class);
            $groupUser = $groupUserRepository->model()->where('group_id', $group->id)->inRandomOrder()->first();
            if ($groupUser) {
                $userId = $groupUser->user_id;
            }
        }

        // Find deadline days
        $deadlineOption = $optionRepository->model()->where('key', '=', 'client_paused_deadline_days')->first();
        if (!$deadlineOption || !$deadlineOption->value) {
            return false;
        }

        // Create task
        $this->taskRepository->create([
            'user_id'     => $userId,
            'client_id'   => $client->id,
            'template_id' => $template->id,
            'active'      => Task::ACTIVE,
            'repeating'   => Task::NOT_REPEATING,
            'deadline'    => Carbon::now()->addDays($deadlineOption->value)->format('Y-m-d H:i:s'),
        ]);

        return true;
    }

    /**
     * Add automatic overdue reason selected in options
     *
     * @param $task
     * @param $option
     * @param string $comment
     */
    protected function addTaskOverdueReason($task, $option, $comment = null)
    {
        // Get overdue reason
        $overdueReason = $this->optionRepository->model()->where('key', '=', $option)->first()->overdueReason;

        $additionalComment = $comment ? ' (' . $comment . ')' : '';
        // Check if we found the overdue reason
        if ($overdueReason) {
            $taskRepository = app()->make(TaskInterface::class);
            $taskRepository->createOverdue($task, [
                'reason'  => $overdueReason->id,
                'user_id' => null,
                'comment' => 'This overdue reason was automatically added to this task.' . $additionalComment,
            ]);
        }
    }

    /**
     * Create task when client set as not paid
     *
     * @param \App\Repositories\Client\Client $client
     * @return boolean
     */
    protected function createNotPaidTask(Client $client)
    {
        // Find template for task on paused client
        $template = $this->optionRepository->model()->where('key', '=', 'client_not_paid_template')->first()->template;
        if (! $template) {
            return false;
        }

        // Find user from group
        $group = $this->optionRepository->model()->where('key', '=', 'client_not_paid_user_group')->first()->group;
        $userId = null;

        if ($group) {
            $groupUser = $this->groupUserRepository->model()->where('group_id', $group->id)->inRandomOrder()->first();

            if ($groupUser) {
                $userId = $groupUser->user_id;
            }
        }

        // Find deadline days
        $deadlineOption = $this->optionRepository->model()->where('key', '=', 'client_not_paid_deadline_days')->first();

        if (! $deadlineOption || ! $deadlineOption->value) {
            return false;
        }

        // Create task
        $this->taskRepository->create([
            'user_id'     => $userId,
            'client_id'   => $client->id,
            'template_id' => $template->id,
            'active'      => Task::ACTIVE,
            'repeating'   => Task::NOT_REPEATING,
            'deadline'    => Carbon::now()->addDays($deadlineOption->value)->format('Y-m-d H:i:s'),
        ]);

        return true;
    }

    /**
     * Create task when client set as not paid
     *
     * @param \App\Repositories\Client\Client $client
     * @return boolean
     */
    protected function createHighRiskTask(Client $client)
    {
        // Find template for task on paused client
        $template = $this->optionRepository->model()->where('key', '=', 'client_high_risk_template')->first()->template;
        if (! $template) {
            return false;
        }

        // Find user from group
        $group = $this->optionRepository->model()->where('key', '=', 'client_high_risk_user_group')->first()->group;
        $userId = null;

        if ($group) {
            $groupUser = $this->groupUserRepository->model()->where('group_id', $group->id)->inRandomOrder()->first();

            if ($groupUser) {
                $userId = $groupUser->user_id;
            }
        }

        // Find deadline days
        $deadlineOption = $this->optionRepository->model()->where('key', '=', 'client_high_risk_deadline_days')->first();

        if (! $deadlineOption || ! $deadlineOption->value) {
            return false;
        }

        // Create task
        $this->taskRepository->create([
            'user_id'     => $userId,
            'client_id'   => $client->id,
            'template_id' => $template->id,
            'active'      => Task::ACTIVE,
            'repeating'   => Task::NOT_REPEATING,
            'deadline'    => Carbon::now()->addDays($deadlineOption->value)->format('Y-m-d H:i:s'),
        ]);

        return true;
    }
}
