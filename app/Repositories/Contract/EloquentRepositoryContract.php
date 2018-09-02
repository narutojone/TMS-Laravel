<?php
 
namespace App\Repositories\Contract;

use App\Helpers\ContractUtils;
use App\Repositories\BaseEloquentRepository;
use App\Repositories\Client\Client;
use App\Repositories\Client\ClientInterface;
use App\Repositories\ContractSalaryDay\ContractSalaryDayInterface;
use App\Repositories\Option\OptionInterface;
use App\Repositories\Task\Task;
use App\Repositories\Task\TaskInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * The eloquent element in a repository should contain all data manipulation related to a entity
 */
class EloquentRepositoryContract extends BaseEloquentRepository implements ContractInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryContract constructor.
     *
     * @param Contract $model
     */
    public function __construct(Contract $model)
    {
        parent::__construct();
        
        $this->model = $model;
    }

    /**
     * Create a new Contract.
     *
     * @param array $input
     * @return Contract
     * @throws ValidationException
     * @throws \Exception
     */
    public function create(array $input)
    {
        $input = $this->prepareCreateData($input);

        if(!$this->isValid('create', $input)) {
            throw new ValidationException($this->validators['create']);
        }

        DB::beginTransaction();
        try {
            // Terminate active contract before creating the new one
            $oldContract = $this->model->where('client_id', $input['client_id'])->where('active', Contract::ACTIVE)->first();
            if ($oldContract) {
                $oldContract->update([
                    'active' => Contract::NOT_ACTIVE,
                    'end_date' => Carbon::now(),
                ]);
            }

            // Create the new contract
            $contract = $this->model->create($input);

            // Create salary days
            if ($input['salary'] == 1) {
                $this->saveSalaryDays($contract, $input['salary_day']);
            }

            // Generate task list
            $this->generateTasks($contract->fresh());
        }
        catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        DB::commit();

        // Send notifications
        $this->sendNotifications($contract, $oldContract);
        return $contract;
    }

    /**
     * Prepare data for insert action
     *
     * @param array $input
     * @return array
     */
    protected function prepareCreateData(array $input)
    {
        // Prepare default data
        $input['active'] = 1;

        // Deactivate all other fields if it's one_time
        if($input['one_time'] == 1) {
            // Set all fields to false
            $input['under_50_bills'] = 0;
            $input['shareholder_registry'] = 0;
            $input['control_client'] = 0;
            $input['bank_reconciliation'] = 0;
            $input['bank_reconciliation_frequency_custom'] = 0;
            $input['bookkeeping'] = 0;
            $input['bookkeeping_frequency_custom'] = 0;
            $input['mva'] = 0;
            $input['financial_statements'] = 0;
            $input['salary_check'] = 0;
            $input['salary'] = 0;

            // End contract and set end_date
            $input['end_date'] = Carbon::now();
            $input['active'] = 0;
        }

        // Auth::user() is not available in migrations so we need to have a default value
        $input['created_by'] = Auth::user() ? Auth::user()->id : 30;

        if($input['control_client'] == 1) {
            $input['bank_reconciliation'] = 0;
            $input['bank_reconciliation_frequency_custom'] = 0;
            $input['bookkeeping'] = 0;
            $input['bookkeeping_frequency_custom'] = 0;
        }

        if($input['under_50_bills'] == 1) {
            $input['control_client'] = 0;
            $input['bookkeeping_frequency_custom'] = 0;
            $input['bank_reconciliation_frequency_custom'] = 0;
        }

        // Prepare default data which is related to other fields
        if($input['bank_reconciliation'] == 0) {
            $input['bank_reconciliation_date'] = null;
        }

        if($input['bookkeeping'] == 0) {
            $input['bookkeeping_date'] = null;
        }

        if($input['mva'] == 0) {
            $input['mva_type'] = null;
        }

        if($input['financial_statements'] == 0) {
            $input['financial_statements_year'] = null;
        }

        if(!isset($input['salary_day']) || $input['salary'] == 0) {
            $input['salary_day'] = [];
        }

        if($input['bank_reconciliation_frequency_custom'] == 0) {
            if($input['mva_type'] == Contract::MVA_TYPE_TERM) {
                $input['bank_reconciliation_frequency'] = '2 months 10';
            }
            else {
                $input['bank_reconciliation_frequency'] = '4 months 10';
            }
        }

        if($input['bookkeeping_frequency_custom'] == 0) {
            $input['bookkeeping_frequency_1'] = '1 months 15';
            if($input['mva_type'] == Contract::MVA_TYPE_TERM) {
                $input['bookkeeping_frequency_2'] = '2 months 10';
            }
            else {
                $input['bookkeeping_frequency_2'] = '4 months 10';
            }
        }

        return $input;
    }

    /**
     * Terminate a contract
     *
     * @param Contract Contract
     * @return Contract
     * @throws \Exception
     */
    public function terminate(Contract $contract)
    {
        DB::beginTransaction();

        try {
            // Update the contract entry
            $contract->update([
                'active' => Contract::NOT_ACTIVE,
                'end_date' => Carbon::now(),
            ]);

            // Delete all client tasks
            $contractUtils = new ContractUtils();
            $contractUtils->setContract($contract->getAttributes());

            $taskRepository = app()->make(TaskInterface::class);
            foreach ($contractUtils->existingClientTasks as $templateId => $tasks) {
                if(is_array($tasks)) {
                    foreach ($tasks as $task) {
                        if (is_null($task->completed_at) && $task->reopened == 0 && $task->regenerated == 0) {
                            $taskRepository->delete($task->id);
                        }
                    }
                }
            }

            // Make the client not active
            $clientRepository = app()->make(ClientInterface::class);
            $clientRepository->update($contract->client->id, [
                'active' => Client::NOT_ACTIVE,
            ]);
        }
        catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }

        DB::commit();

        return $contract;
    }

    /**
     * Store salary days for a new contract
     *
     * @param Contract $contract
     * @param array $salaryDays
     */
    private function saveSalaryDays(Contract $contract, array $salaryDays) {
        $salaryDaysRepository = app()->make(ContractSalaryDayInterface::class);
        foreach ($salaryDays as $salaryDay) {
            $salaryDaysRepository->create([
                'contract_id' => $contract->id,
                'day'         => $salaryDay,
            ]);
        }
    }

    private function generateTasks(Contract $contract)
    {
        $contractUtils = new ContractUtils();

        $contractAttributes = $contract->getAttributes();
        foreach ($contract->salaryDays as $salaryDay) {
            $contractAttributes['salary_day'][] = $salaryDay->day;
        }
        $contractUtils->setContract($contractAttributes);

        $newTaskList = $contractUtils->generateNewContractTaskList();

        $this->updateContractRelatedTasks($contract, $contractUtils->existingClientTasks, $newTaskList);
    }

    private function updateContractRelatedTasks(Contract $contract, array $existingTaskList, array $newTaskList)
    {
        $taskRepository = app()->make(TaskInterface::class);

        // Delete tasks that are meant to be initially deleted
        if(isset($newTaskList['tasksToBeDeleted'])) {
            foreach ($newTaskList['tasksToBeDeleted'] as $task) {
                $taskRepository->delete($task->id);
            }
        }

        // Update tasks that are meant to be updated
        if(isset($newTaskList['tasksToBeUpdated'])) {
            foreach ($newTaskList['tasksToBeUpdated'] as $task) {
                $taskRepository->update($task['tmsTaskId'], [
                    'repeating' => $task['repeating'],
                    'frequency' => $task['frequency'],
                    'deadline'  => Carbon::parse($task['deadline'])->format('Y-m-d H:i:s'),
                ]);
            }
        }

        // create new tasks
        if(isset($newTaskList['newTasks'])) {
            foreach ($newTaskList['newTasks'] as $task) {
                $taskRepository->create([
                    'template_id' => $task['template'],
                    'client_id'   => $contract->client->id,
                    'user_id'     => ($task['template'] == 71 || $task['template'] == 77) ? 30 : $contract->client->employee_id, // Custom rule for Jonah (RN)
                    'repeating'   => $task['repeating'],
                    'frequency'   => $task['frequency'],
                    'deadline'    => Carbon::parse($task['deadline'])->format('Y-m-d H:i:s'),
                    'active'      => $contract->client->paid && !$contract->client->paused ? 1 : 0, // Set active based on client paid & paused status
                ]);
            }
        }

    }

    /**
     * Send notification to client when a new contract is created/updated
     * A contract is considered created when there is no other active contract in place
     *
     * @param Contract $newContract
     * @param Contract|null $oldContract
     */
    protected function sendNotifications(Contract $newContract, ?Contract $oldContract)
    {
        $clientEmail = $newContract->client->email();
        if(!$clientEmail) {
            return;
        }

        $optionRepository = app()->make(OptionInterface::class);
        $emailTemplateKey = $oldContract ? 'contract_updated_email_template' : 'contract_created_email_template';

        $emailTemplate = $optionRepository->model()->where('key', '=', $emailTemplateKey)->first()->emailTemplate;


        // Prepare contract data and format
        $contractData = 'Oppgavene vi skal gjøre ifølge avtalen er:<br>';
        if($newContract->one_time){
            $contractData .= '- Enkelttimer<br>';
        } else {
            if($newContract->shareholder_registry){
                $contractData .= '- Aksjonærregisteroppgave<br>';
            }
            if($newContract->bank_reconciliation){
                $contractData .= '- Avstemming (Fra og med: ' . Carbon::parse($newContract->bank_reconciliation_date)->format('d/m/Y') . ')<br>';
            }
            if($newContract->bookkeeping){
                $contractData .= '- Bokføring (Fra og med: ' . Carbon::parse($newContract->bookkeeping_date)->format('d/m/Y');
                if($newContract->under_50_bills){
                $contractData .= ' - Under 50 bilag i året';
                }
                $contractData .= ')<br>';
            }
            if(!$newContract->bookkeeping || !$newContract->bank_reconciliation){
                $contractData .= '- Kontroll av regnskap<br>';
            }
            if($newContract->mva && $newContract->bookkeeping && $newContract->bank_reconciliation && $newContract->financial_statements){
                $contractData .= '- MVA<br>';
            }
            if($newContract->financial_statements){
                $contractData .= '- Årsoppgjør (For regnskapsåret ' . $newContract->financial_statements_year . ' og utover)<br>';
            }
            if($newContract->salary_check){
                $contractData .= '- Kontroll av lønn (Kunde fører lønn selv)<br>';
            }
            if($newContract->salary){
                $contractData .= '- Lønn<br>';  
            }

            // Find company MVA type
            $mvaType = $mvaNoType = '';
            if($newContract->mva_type){
                if($newContract->mva_type == 1){
                    $mvaType = ' (Termin)';
                } else {
                    $mvaType = ' (Årlig)';
                }
            } else {
                $mvaNoType = ' IKKE';
            }

            // Add MVA type to contractData
            $contractData .= '<br>Din bedrift står' . $mvaNoType .' registrert som MVA-pliktig' . $mvaType . ' i våre interne systemer.<br><br>';
            
        }

        $data = [
            'template_id'                       => $emailTemplate->id,
            'clientname'                        => $newContract->client->name,
            'employeename'                      => $newContract->client->employee ? $newContract->client->employee->name : '',
            'employeepf'                        => $newContract->client->employee ? 'PF'.$newContract->client->employee->pf_id : '',
            'contract_start_date'               => Carbon::parse($newContract->start_date)->format('d/m/Y'),
            'contract_data'                     => $contractData,
        ];

        $response = notification('template')
            ->template($emailTemplate->id)
            ->subject($emailTemplate->title)
            ->to($clientEmail)
            ->from('')
            ->data($data)
            ->saveForApproving(null, $newContract->client->name, null);
    }
}