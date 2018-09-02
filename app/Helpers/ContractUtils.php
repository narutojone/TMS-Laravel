<?php

namespace App\Helpers;

use App\Frequency;
use App\Repositories\Client\Client;
use App\Repositories\Client\ClientInterface;
use App\Repositories\Contract\Contract;
use App\Repositories\Task\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ContractUtils
{
    private $contract;

    private $client;

    // $existingClientTasks contains all client tasks BEFORE the new contract
    public $existingClientTasks;

    // $allowedTaskTemplates contains all template ID's that can be handled by contracts
    public $allowedTaskTemplates = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 21, 22, 25, 26, 27, 35, 41, 47, 71, 75, 77];

    public function __construct()
    {

    }

    public function setContract($attributes = [])
    {
        // Set contract details
        $this->contract = $attributes;

        // Set client which will receive the contract
        $clientRepository = app()->make(ClientInterface::class);
        $this->client = $clientRepository->find($attributes['client_id']);

        // Fetch existing client tasks
        $this->existingClientTasks = $this->getExistingClientTasks();
    }

    public function generateNewContractTaskList()
    {
        $tasksList = [
            'client_id' => $this->client->id,
            'user_id'   => $this->client->employee ? $this->client->employee->id : null,
        ];

        // Set min. date on which a task can be created
        if (strtotime($this->contract['start_date']) < strtotime('now')) {
            $referenceDate = date('Y-m-d', strtotime('now'));
        }
        else {
            $referenceDate = $this->contract['start_date'];
        }

        if ($this->contract['one_time'] == 1){

            // ID:   #35
            // Name: Enkelttimer
            $deadline = date('Y-m-d H:i:s', strtotime("+3 days"));
            $tasksAttributes = ['template' => 35, 'repeating' => false, 'frequency' => '', 'deadline' => $deadline];

            $tasksList['newTasks'][] = $tasksAttributes;

            // ID:   #77  -  special rule so that these tasks should only be applied to new customers
            // Name: Onboard client - One time contract
            $deadline = date('Y-m-d H:i:s', strtotime("+1 days"));
            $tasksAttributes = ['template' => 77, 'repeating' => false, 'frequency' => '', 'deadline' => $deadline];

            $existingTask = $this->clientHasTaskTemplateId(77);

            if($existingTask === false) {
                $tasksList['newTasks'][] = $tasksAttributes;
            }
            else {
                $tasksList['tasksToBeSkipped'][] = $existingTask;
            }
        }
        else {
            // Check if the client is below 50 bills a year
            // ID:   #75
            // Name: Selskap m/ lite aktivitet (under 50 bilag)
            if ($this->contract['under_50_bills']){
                $deadline = $this->generateDate(['months' => [3], 'days' => [10], 'currentDate'=>$referenceDate]); // Always use next appearance of 31st of January
                $tasksAttributes = ['template' => 75, 'repeating' => true, 'frequency' => '12 months 10', 'deadline' => $deadline];

                // Check if client already has an active task for this template
                $activeTask = $this->getLatestActiveTask(75);
                if($activeTask === false) {
                    // check for latest deadline of completed tasks (completed, re-opened and regenerated)
                    $existingInactiveTask = $this->getLatestInactiveTask(75);
                    if($existingInactiveTask !== false) {
                        if(strtotime($existingInactiveTask->deadline) >= strtotime($tasksAttributes['deadline'])) {
                            $tasksAttributes['deadline'] = $this->generateDate(['months' => [3], 'days' => [10], 'currentDate'=>$existingInactiveTask->deadline]);
                        }
                    }
                    $tasksList['newTasks'][] = $tasksAttributes;
                }
                else {
                    // Check if frequency is correct AND deadline is in allowed months
                    if( ($activeTask->frequency==$tasksAttributes['frequency']) && $this->deadlineIsAllowed($activeTask->deadline, ['months' => [3], 'days' => [10]]) ) {
                        $tasksList['tasksToBeSkipped'][] = $activeTask;
                    }
                    else {
                        $tasksList['tasksToBeUpdated'][] = $tasksAttributes + ['tmsTaskId'=>$activeTask->id];
                    }
                }
            } else {
                // Skip all tasks the client can't have if this task does not apply.

                // ID:   #6
                // Name: Avstemming (termin MVA)
                if($this->contract['bank_reconciliation'] == 1) {
                    if ($this->contract['mva'] == 1 && $this->contract['mva_type'] == Contract::MVA_TYPE_TERM) {
                        $allowedMonths = [2, 4, 6, 8, 10, 12];
                        if($this->contract['bank_reconciliation_frequency'] == '1 months 10') {
                            $allowedMonths = range(1, 12);
                        }

                        $deadline = $this->generateDate(['months' => $allowedMonths, 'days' => [10], 'currentDate'=>$this->contract['bank_reconciliation_date']]);
                        // Skip one occurrence on deadline
                        $deadline = (new Frequency($this->contract['bank_reconciliation_frequency']))->next($deadline);
                        $tasksAttributes = ['template'=>6, 'repeating' => true, 'frequency' => $this->contract['bank_reconciliation_frequency'], 'deadline' => $deadline];

                        // Check if client already has an active task for this template
                        $activeTask = $this->getLatestActiveTask(6);
                        if($activeTask === false) {
                            // check for latest deadline of completed tasks (completed, re-opened and regenerated)
                            $existingInactiveTask = $this->getLatestInactiveTask(6);
                            if($existingInactiveTask !== false) {
                                if(strtotime($existingInactiveTask->deadline) >= strtotime($tasksAttributes['deadline'])) {
                                    $tasksAttributes['deadline'] = $this->generateDate(['months' => $allowedMonths, 'days' => [10], 'currentDate'=>$existingInactiveTask->deadline]);
                                }
                            }
                            $tasksList['newTasks'][] = $tasksAttributes;
                        }
                        else {
                            // Check if frequency is correct AND deadline is in allowed months
                            if( ($activeTask->frequency==$tasksAttributes['frequency']) && $this->deadlineIsAllowed($activeTask->deadline, ['months' => $allowedMonths, 'days' => [10]]) ) {
                                $tasksList['tasksToBeSkipped'][] = $activeTask;
                            }
                            else {
                                $tasksList['tasksToBeUpdated'][] = $tasksAttributes + ['tmsTaskId'=>$activeTask->id];
                            }
                        }
                    } else {
                        // ID:   #7
                        // Name: Avstemming (årlig MVA / ikke MVA)
                        $allowedMonths = [2, 6, 10];
                        if($this->contract['bank_reconciliation_frequency'] == '1 months 10') {
                            $allowedMonths = range(1, 12);
                        }
                        elseif($this->contract['bank_reconciliation_frequency'] == '2 months 10') {
                            $allowedMonths = [2,4,6,8,10,12];
                        }
                        elseif($this->contract['bank_reconciliation_frequency'] == '3 months 10') {
                            $allowedMonths = [2,5,8,11];
                        }

                        $deadline = $this->generateDate(['months' => $allowedMonths, 'days' => [10], 'currentDate'=>$this->contract['bank_reconciliation_date']]);
                        // Skip one occurrence on deadline
                        $deadline = (new Frequency($this->contract['bank_reconciliation_frequency']))->next($deadline);
                        $tasksAttributes = ['template'=>7, 'repeating' => true, 'frequency' => $this->contract['bank_reconciliation_frequency'], 'deadline' => $deadline];

                        // Check if client already has an active task for this template
                        $activeTask = $this->getLatestActiveTask(7);
                        if($activeTask === false) {
                            // check for latest deadline of completed tasks (completed, re-opened and regenerated)
                            $existingInactiveTask = $this->getLatestInactiveTask(7);
                            if($existingInactiveTask !== false) {
                                if(strtotime($existingInactiveTask->deadline) >= strtotime($tasksAttributes['deadline'])) {
                                    $tasksAttributes['deadline'] = $this->generateDate(['months' => $allowedMonths, 'days' => [10], 'currentDate'=>$existingInactiveTask->deadline]);
                                }
                            }
                            $tasksList['newTasks'][] = $tasksAttributes;
                        }
                        else {
                            // Check if frequency is correct AND deadline is in allowed months
                            if( ($activeTask->frequency==$tasksAttributes['frequency']) && $this->deadlineIsAllowed($activeTask->deadline, ['months' => $allowedMonths, 'days' => [10]]) ) {
                                $tasksList['tasksToBeSkipped'][] = $activeTask;
                            }
                            else {
                                $tasksList['tasksToBeUpdated'][] = $tasksAttributes + ['tmsTaskId'=>$activeTask->id];
                            }
                        }
                    }
                }

                // ID:   #1
                // Name: Bokføring (regnskap a jour)
                if ($this->contract['bookkeeping'] == 1) {
                    $allowedMonths = range(1, 12);
                    if($this->contract['bookkeeping_frequency_1'] == '2 months 15') {
                        $allowedMonths = [2,4,6,8,10,12];
                    }
                    elseif($this->contract['bookkeeping_frequency_1'] == '3 months 15') {
                        $allowedMonths = [2,5,8,11];
                    }
                    elseif($this->contract['bookkeeping_frequency_1'] == '4 months 15') {
                        $allowedMonths = [2,6,10];
                    }

                    $deadline = $this->generateDate(['months'=>$allowedMonths, 'days'=>[15], 'currentDate'=>$this->contract['bookkeeping_date']]);
                    // Skip one occurrence on deadline
                    $deadline = (new Frequency($this->contract['bookkeeping_frequency_1']))->next($deadline);
                    $tasksAttributes = ['template'=>1, 'repeating'=>true, 'frequency'=>$this->contract['bookkeeping_frequency_1'], 'deadline'=>$deadline ];

                    // Check if client already has an active task for this template
                    $activeTask = $this->getLatestActiveTask(1);
                    if($activeTask === false) {
                        // check for latest deadline of completed tasks (completed, re-opened and regenerated)
                        $existingInactiveTask = $this->getLatestInactiveTask(1);
                        if($existingInactiveTask !== false) {
                            if(strtotime($existingInactiveTask->deadline) >= strtotime($tasksAttributes['deadline'])) {
                                $tasksAttributes['deadline'] = $this->generateDate(['months'=>$allowedMonths, 'days'=>[15], 'currentDate'=>$existingInactiveTask->deadline]);
                            }
                        }
                        $tasksList['newTasks'][] = $tasksAttributes;
                    }
                    else {
                        // Check if frequency is correct AND deadline is in allowed months
                        if( ($activeTask->frequency==$tasksAttributes['frequency']) && $this->deadlineIsAllowed($activeTask->deadline, ['months'=>$allowedMonths, 'days'=>[15]]) ) {
                            $tasksList['tasksToBeSkipped'][] = $activeTask;
                        }
                        else {
                            $tasksList['tasksToBeUpdated'][] = $tasksAttributes + ['tmsTaskId'=>$activeTask->id];
                        }
                    }
                }

                if ($this->contract['bookkeeping'] == 1) {
                    // ID:   #2
                    // Name: Bokføring (termin MVA)
                    if ($this->contract['mva'] == 1 && $this->contract['mva_type'] == Contract::MVA_TYPE_TERM)
                    {
                        $allowedMonths = [2, 4, 6, 8, 10, 12];
                        if($this->contract['bookkeeping_frequency_2'] == '1 months 10') {
                            $allowedMonths = range(1, 12);
                        }

                        $deadline = $this->generateDate(['months' => $allowedMonths, 'days' => [10], 'currentDate'=>$this->contract['bookkeeping_date']]);
                        // Skip one occurrence on deadline
                        $deadline = (new Frequency($this->contract['bookkeeping_frequency_2']))->next($deadline);
                        $tasksAttributes = ['template'=>2, 'repeating' => true, 'frequency' => $this->contract['bookkeeping_frequency_2'], 'deadline' => $deadline];

                        // Check if client already has an active task for this template
                        $activeTask = $this->getLatestActiveTask(2);
                        if($activeTask === false) {
                            // check for latest deadline of completed tasks (completed, re-opened and regenerated)
                            $existingInactiveTask = $this->getLatestInactiveTask(2);
                            if($existingInactiveTask !== false) {
                                if(strtotime($existingInactiveTask->deadline) >= strtotime($tasksAttributes['deadline'])) {
                                    $tasksAttributes['deadline'] = $this->generateDate(['months' => $allowedMonths, 'days' => [10], 'currentDate'=>$existingInactiveTask->deadline]);
                                }
                            }
                            $tasksList['newTasks'][] = $tasksAttributes;
                        }
                        else {
                            // Check if frequency is correct AND deadline is in allowed months
                            if( ($activeTask->frequency==$tasksAttributes['frequency']) && $this->deadlineIsAllowed($activeTask->deadline, ['months' => $allowedMonths, 'days' => [10]]) ) {
                                $tasksList['tasksToBeSkipped'][] = $activeTask;
                            }
                            else {
                                $tasksList['tasksToBeUpdated'][] = $tasksAttributes + ['tmsTaskId'=>$activeTask->id];
                            }
                        }
                    }
                    else
                    {
                        // ID:   #3
                        // Name: Bokføring (årlig MVA / ikke MVA)
                        $allowedMonths = [2, 6, 10];
                        if($this->contract['bookkeeping_frequency_2'] == '1 months 10') {
                            $allowedMonths = range(1, 12);
                        }
                        elseif($this->contract['bookkeeping_frequency_2'] == '2 months 10') {
                            $allowedMonths = [2,4,6,8,10,12];
                        }
                        elseif($this->contract['bookkeeping_frequency_2'] == '3 months 10') {
                            $allowedMonths = [2,5,8,11];
                        }

                        $deadline = $this->generateDate(['months' => $allowedMonths, 'days' => [10], 'currentDate'=>$this->contract['bookkeeping_date']]);
                        // Skip one occurrence on deadline
                        $deadline = (new Frequency($this->contract['bookkeeping_frequency_2']))->next($deadline);
                        $tasksAttributes = ['template'=>3, 'repeating' => true, 'frequency' => $this->contract['bookkeeping_frequency_2'], 'deadline' => $deadline];
                        
                        // Check if client already has an active task for this template
                        $activeTask = $this->getLatestActiveTask(3);
                        if($activeTask === false) {
                            // check for latest deadline of completed tasks (completed, re-opened and regenerated)
                            $existingInactiveTask = $this->getLatestInactiveTask(3);
                            if($existingInactiveTask !== false) {
                                if(strtotime($existingInactiveTask->deadline) >= strtotime($tasksAttributes['deadline'])) {
                                    $tasksAttributes['deadline'] = $this->generateDate(['months' => $allowedMonths, 'days' => [10], 'currentDate'=>$existingInactiveTask->deadline]);
                                }
                            }
                            $tasksList['newTasks'][] = $tasksAttributes;
                        }
                        else {
                            // Check if frequency is correct AND deadline is in allowed months
                            if( ($activeTask->frequency==$tasksAttributes['frequency']) && $this->deadlineIsAllowed($activeTask->deadline, ['months' => $allowedMonths, 'days' => [10]]) ) {
                                $tasksList['tasksToBeSkipped'][] = $activeTask;
                            }
                            else {
                                $tasksList['tasksToBeUpdated'][] = $tasksAttributes + ['tmsTaskId'=>$activeTask->id];
                            }
                        }
                    }
                }

                // ID:   #8
                // Name: Kontroll av regnskap (termin MVA)
                if ($this->contract['financial_statements']==1 && ($this->contract['bookkeeping'] == 0 || $this->contract['bank_reconciliation'] == 0) && $this->contract['mva'] == 1 && $this->contract['mva_type'] == Contract::MVA_TYPE_TERM) {
                    $deadline = $this->generateDate(['months'=>[2,4,6,8,10,12], 'days'=>[10], 'currentDate'=>$referenceDate]);
                    $tasksAttributes = ['template'=>8, 'repeating'=>true, 'frequency'=>'2 months 10', 'deadline'=>$deadline];

                    // Check if client already has an active task for this template
                    $activeTask = $this->getLatestActiveTask(8);
                    if($activeTask === false) {
                        // check for latest deadline of completed tasks (completed, re-opened and regenerated)
                        $existingInactiveTask = $this->getLatestInactiveTask(8);
                        if($existingInactiveTask !== false) {
                            if(strtotime($existingInactiveTask->deadline) >= strtotime($tasksAttributes['deadline'])) {
                                $tasksAttributes['deadline'] = $this->generateDate(['months'=>[2,4,6,8,10,12], 'days'=>[10], 'currentDate'=>$existingInactiveTask->deadline]);
                            }
                        }
                        $tasksList['newTasks'][] = $tasksAttributes;
                    }
                    else {
                        // Check if frequency is correct AND deadline is in allowed months
                        if( ($activeTask->frequency==$tasksAttributes['frequency']) && $this->deadlineIsAllowed($activeTask->deadline, ['months'=>[2,4,6,8,10,12], 'days'=>[10]]) ) {
                            $tasksList['tasksToBeSkipped'][] = $activeTask;
                        }
                        else {
                            $tasksList['tasksToBeUpdated'][] = $tasksAttributes + ['tmsTaskId'=>$activeTask->id];
                        }
                    }
                }

                // ID:   #9
                // Name: Kontroll av regnskap (årlig MVA / ikke MVA)
                if ($this->contract['financial_statements']==1 && ($this->contract['bookkeeping'] == 0 || $this->contract['bank_reconciliation'] == 0) && ( $this->contract['mva'] == 0 || ($this->contract['mva'] == 1 && $this->contract['mva_type'] == Contract::MVA_TYPE_YEARLY)) ) {
                    $deadline = $this->generateDate(['months'=>[2,6,10], 'days'=>[10], 'currentDate'=>$referenceDate]);
                    $tasksAttributes = ['template'=>9, 'repeating'=>true, 'frequency'=>'4 months 10', 'deadline'=>$deadline ];

                    // Check if client already has an active task for this template
                    $activeTask = $this->getLatestActiveTask(9);
                    if($activeTask === false) {
                        // check for latest deadline of completed tasks (completed, re-opened and regenerated)
                        $existingInactiveTask = $this->getLatestInactiveTask(9);
                        if($existingInactiveTask !== false) {
                            if(strtotime($existingInactiveTask->deadline) >= strtotime($tasksAttributes['deadline'])) {
                                $tasksAttributes['deadline'] = $this->generateDate(['months'=>[2,6,10], 'days'=>[10], 'currentDate'=>$existingInactiveTask->deadline]);
                            }
                        }
                        $tasksList['newTasks'][] = $tasksAttributes;
                    }
                    else {
                        // Check if frequency is correct AND deadline is in allowed months
                        if( ($activeTask->frequency==$tasksAttributes['frequency']) && $this->deadlineIsAllowed($activeTask->deadline, ['months'=>[2,6,10], 'days'=>[10]]) ) {
                            $tasksList['tasksToBeSkipped'][] = $activeTask;
                        }
                        else {
                            $tasksList['tasksToBeUpdated'][] = $tasksAttributes + ['tmsTaskId'=>$activeTask->id];
                        }
                    }
                }
            }

            // ID: #10
            // Name: Aksjonærregisteroppgave
            if ($this->contract['shareholder_registry'] == 1) {
                $deadline = $this->generateDate(['months' => [1], 'days' => [31], 'currentDate'=>$referenceDate]); // Always use next appearance of 31st of January
                $tasksAttributes = ['template' => 10, 'repeating' => true, 'frequency' => '12 months end', 'deadline' => $deadline];

                // Check if client already has an active task for this template
                $activeTask = $this->getLatestActiveTask(10);
                if($activeTask === false) {
                    // check for latest deadline of completed tasks (completed, re-opened and regenerated)
                    $existingInactiveTask = $this->getLatestInactiveTask(10);
                    if($existingInactiveTask !== false) {
                        if(strtotime($existingInactiveTask->deadline) >= strtotime($tasksAttributes['deadline'])) {
                            $tasksAttributes['deadline'] = $this->generateDate(['months' => [1], 'days' => [31], 'currentDate'=>$existingInactiveTask->deadline]);
                        }
                    }
                    $tasksList['newTasks'][] = $tasksAttributes;
                }
                else {
                    // Check if frequency is correct AND deadline is in allowed months
                    if( ($activeTask->frequency==$tasksAttributes['frequency']) && $this->deadlineIsAllowed($activeTask->deadline, ['months' => [1], 'days' => [31]]) ) {
                        $tasksList['tasksToBeSkipped'][] = $activeTask;
                    }
                    else {
                        $tasksList['tasksToBeUpdated'][] = $tasksAttributes + ['tmsTaskId'=>$activeTask->id];
                    }
                }
            }

            // ID:   #4
            // Name: Omsetningsoppgave (termin MVA)
            if ($this->contract['mva'] == 1 && $this->contract['mva_type'] == Contract::MVA_TYPE_TERM && $this->contract['bank_reconciliation'] == 1 && $this->contract['bookkeeping'] == 1) {
                $deadline = $this->generateDate(['months' => [2, 4, 6, 8, 10, 12], 'days' => [10], 'currentDate' => $referenceDate]);
                $tasksAttributes = ['template' => 4, 'repeating' => true, 'frequency' => '2 months 10', 'deadline' => $deadline];

                // Check if client already has an active task for this template
                $activeTask = $this->getLatestActiveTask(4);
                if($activeTask === false) {
                    // check for latest deadline of completed tasks (completed, re-opened and regenerated)
                    $existingInactiveTask = $this->getLatestInactiveTask(4);
                    if($existingInactiveTask !== false) {
                        if(strtotime($existingInactiveTask->deadline) >= strtotime($tasksAttributes['deadline'])) {
                            $tasksAttributes['deadline'] = $this->generateDate(['months' => [2, 4, 6, 8, 10, 12], 'days' => [10], 'currentDate'=>$existingInactiveTask->deadline]);
                        }
                    }
                    $tasksList['newTasks'][] = $tasksAttributes;
                }
                else {
                    // Check if frequency is correct AND deadline is in allowed months
                    if( ($activeTask->frequency==$tasksAttributes['frequency']) && $this->deadlineIsAllowed($activeTask->deadline, ['months' => [2, 4, 6, 8, 10, 12], 'days' => [10]]) ) {
                        $tasksList['tasksToBeSkipped'][] = $activeTask;
                    }
                    else {
                        $tasksList['tasksToBeUpdated'][] = $tasksAttributes + ['tmsTaskId'=>$activeTask->id];
                    }
                }
            }

            // ID:   #5
            // Name: Omsetningsoppgave (årlig MVA)
            if ($this->contract['mva'] == 1 && $this->contract['mva_type'] == Contract::MVA_TYPE_YEARLY && $this->contract['bank_reconciliation'] == 1 && $this->contract['bookkeeping'] == 1) {
                $deadline = $this->generateDate(['months' => [3], 'days' => [10], 'currentDate' => $referenceDate]);
                $tasksAttributes = ['template' => 5, 'repeating' => true, 'frequency' => '12 months 10', 'deadline' => $deadline];

                // Check if client already has an active task for this template
                $activeTask = $this->getLatestActiveTask(5);
                if($activeTask === false) {
                    // check for latest deadline of completed tasks (completed, re-opened and regenerated)
                    $existingInactiveTask = $this->getLatestInactiveTask(5);
                    if($existingInactiveTask !== false) {
                        if(strtotime($existingInactiveTask->deadline) >= strtotime($tasksAttributes['deadline'])) {
                            $tasksAttributes['deadline'] = $this->generateDate(['months' => [3], 'days' => [10], 'currentDate'=>$existingInactiveTask->deadline]);
                        }
                    }
                    $tasksList['newTasks'][] = $tasksAttributes;
                }
                else {
                    // Check if frequency is correct AND deadline is in allowed months
                    if( ($activeTask->frequency==$tasksAttributes['frequency']) && $this->deadlineIsAllowed($activeTask->deadline, ['months' => [3], 'days' => [10]]) ) {
                        $tasksList['tasksToBeSkipped'][] = $activeTask;
                    }
                    else {
                        $tasksList['tasksToBeUpdated'][] = $tasksAttributes + ['tmsTaskId'=>$activeTask->id];
                    }
                }
            }

            // ID:   #11
            // Name: Klargjøring til årsoppgjøret
            if($this->contract['financial_statements']==1) {
                $deadline = Carbon::parse(((int)$this->contract['financial_statements_year']+1).'-03-31 23:59:00'); // Always use next financial statement year with 1st of March
                $tasksAttributes = ['template'=>11, 'repeating'=>true, 'frequency'=>'12 months end', 'deadline'=>$deadline];

                // Check if client already has an active task for this template
                $activeTask = $this->getLatestActiveTask(11);
                if($activeTask === false) {
                    // check for latest deadline of completed tasks (completed, re-opened and regenerated)
                    $existingInactiveTask = $this->getLatestInactiveTask(11);
                    if($existingInactiveTask !== false) {
                        if(strtotime($existingInactiveTask->deadline) >= strtotime($tasksAttributes['deadline'])) {
                            $tasksAttributes['deadline'] = $this->generateDate(['months' => [3], 'days' => [31], 'currentDate'=>$existingInactiveTask->deadline]);
                        }
                    }
                    $tasksList['newTasks'][] = $tasksAttributes;
                }
                else {
                    // Check if frequency is correct AND deadline is in allowed months
                    if( ($activeTask->frequency==$tasksAttributes['frequency']) && $this->deadlineIsAllowed($activeTask->deadline, ['months' => [3], 'days' => [31]]) ) {
                        $tasksList['tasksToBeSkipped'][] = $activeTask;
                    }
                    else {
                        $tasksList['tasksToBeUpdated'][] = $tasksAttributes + ['tmsTaskId'=>$activeTask->id];
                    }
                }
            }

            // ID:   #22
            // Name: Oppfølging årsoppgjør (AS)
            if ($this->client->type == Client::TYPE_AS && $this->contract['financial_statements']==1) {
                $deadline = Carbon::parse(((int)$this->contract['financial_statements_year']+1).'-06-01 23:59:00'); // Always use next financial statement year with 15th of May
                $tasksAttributes = ['template' => 22, 'repeating' => true, 'frequency' => '12 months 1', 'deadline' => $deadline];

                // Check if client already has an active task for this template
                $activeTask = $this->getLatestActiveTask(22);
                if($activeTask === false) {
                    // check for latest deadline of completed tasks (completed, re-opened and regenerated)
                    $existingInactiveTask = $this->getLatestInactiveTask(22);
                    if($existingInactiveTask !== false) {
                        if(strtotime($existingInactiveTask->deadline) >= strtotime($tasksAttributes['deadline'])) {
                            $tasksAttributes['deadline'] = $this->generateDate(['months' => [6], 'days' => [01], 'currentDate'=>$existingInactiveTask->deadline]);
                        }
                    }
                    $tasksList['newTasks'][] = $tasksAttributes;
                }
                else {
                    // Check if frequency is correct AND deadline is in allowed months
                    if( ($activeTask->frequency==$tasksAttributes['frequency']) && $this->deadlineIsAllowed($activeTask->deadline, ['months' => [6], 'days' => [01]]) ) {
                        $tasksList['tasksToBeSkipped'][] = $activeTask;
                    }
                    else {
                        $tasksList['tasksToBeUpdated'][] = $tasksAttributes + ['tmsTaskId'=>$activeTask->id];
                    }
                }
            }

            // ID:   #21
            // Name: Oppfølging årsoppgjør (ENK)
            if ($this->client->type == Client::TYPE_ENK && $this->contract['financial_statements']==1) {
                $deadline = Carbon::parse(((int)$this->contract['financial_statements_year']+1).'-06-01 23:59:00'); // Always use next financial statement year with 15th of May
                $tasksAttributes = ['template' => 21, 'repeating' => true, 'frequency' => '12 months 1', 'deadline' => $deadline];

                // Check if client already has an active task for this template
                $activeTask = $this->getLatestActiveTask(21);
                if($activeTask === false) {
                    // check for latest deadline of completed tasks (completed, re-opened and regenerated)
                    $existingInactiveTask = $this->getLatestInactiveTask(21);
                    if($existingInactiveTask !== false) {
                        if(strtotime($existingInactiveTask->deadline) >= strtotime($tasksAttributes['deadline'])) {
                            $tasksAttributes['deadline'] = $this->generateDate(['months' => [6], 'days' => [01], 'currentDate'=>$existingInactiveTask->deadline]);
                        }
                    }
                    $tasksList['newTasks'][] = $tasksAttributes;
                }
                else {
                    // Check if frequency is correct AND deadline is in allowed months
                    if( ($activeTask->frequency==$tasksAttributes['frequency']) && $this->deadlineIsAllowed($activeTask->deadline, ['months' => [6], 'days' => [01]]) ) {
                        $tasksList['tasksToBeSkipped'][] = $activeTask;
                    }
                    else {
                        $tasksList['tasksToBeUpdated'][] = $tasksAttributes + ['tmsTaskId'=>$activeTask->id];
                    }
                }
            }

            // ID:   #12
            // Name: Årsoppgjør (AS)
            if ($this->client->type == Client::TYPE_AS && $this->contract['financial_statements']==1) {
                $deadline = Carbon::parse(((int)$this->contract['financial_statements_year']+1).'-05-31 23:59:00');
                $tasksAttributes = ['template'=>12, 'repeating'=>true, 'frequency'=>'12 months end', 'deadline'=>$deadline];

                // Check if client already has an active task for this template
                $activeTask = $this->getLatestActiveTask(12);
                if($activeTask === false) {
                    // check for latest deadline of completed tasks (completed, re-opened and regenerated)
                    $existingInactiveTask = $this->getLatestInactiveTask(12);
                    if($existingInactiveTask !== false) {
                        if(strtotime($existingInactiveTask->deadline) >= strtotime($tasksAttributes['deadline'])) {
                            $tasksAttributes['deadline'] = $this->generateDate(['months' => [5], 'days' => [31], 'currentDate'=>$existingInactiveTask->deadline]);
                        }
                    }
                    $tasksList['newTasks'][] = $tasksAttributes;
                }
                else {
                    $tasksList['tasksToBeSkipped'][] = $activeTask;
                }
            }

            // ID:   #13
            // Name: Årsoppgjør (ENK)
            if ($this->client->type == Client::TYPE_ENK && $this->contract['financial_statements'] == 1) {
                $deadline = Carbon::parse(((int)$this->contract['financial_statements_year']+1).'-05-31 23:59:00');
                $tasksAttributes = ['template'=>13, 'repeating'=>true, 'frequency'=>'12 months end', 'deadline'=>$deadline ];

                // Check if client already has an active task for this template
                $activeTask = $this->getLatestActiveTask(13);
                if($activeTask === false) {
                    // check for latest deadline of completed tasks (completed, re-opened and regenerated)
                    $existingInactiveTask = $this->getLatestInactiveTask(13);
                    if($existingInactiveTask !== false) {
                        if(strtotime($existingInactiveTask->deadline) >= strtotime($tasksAttributes['deadline'])) {
                            $tasksAttributes['deadline'] = $this->generateDate(['months' => [5], 'days' => [31], 'currentDate'=>$existingInactiveTask->deadline]);
                        }
                    }
                    $tasksList['newTasks'][] = $tasksAttributes;
                }
                else {
                    $tasksList['tasksToBeSkipped'][] = $activeTask;
                }
            }

            // ID:   #41  -  special rule so that these tasks should only be applied to new customers
            // Name: Ny kunde
            if((int)$this->client->id > 642 ) {
                $deadline = date('Y-m-d', strtotime("+2 days"));
                $tasksAttributes = ['template' => 41, 'repeating' => false, 'frequency' => '', 'deadline' => $deadline];

                $existingTask = $this->clientHasTaskTemplateId(41);

                if($existingTask === false) {
                    $tasksList['newTasks'][] = $tasksAttributes;
                }
                else {
                    $tasksList['tasksToBeSkipped'][] = $existingTask;
                }
            }

            // ID:   #27
            // Name: Lønn (termin)
            if ($this->contract['salary'] == 1) {
                $deadline = $this->generateDate(['months' => [1, 3, 5, 7, 9, 11], 'days' => [15], 'currentDate' => $referenceDate]);
                $tasksAttributes = ['template' => 27, 'repeating' => true, 'frequency' => '2 months 15', 'deadline' => $deadline];

                // Check if client already has an active task for this template
                $activeTask = $this->getLatestActiveTask(27);
                if($activeTask === false) {
                    // check for latest deadline of completed tasks (completed, re-opened and regenerated)
                    $existingInactiveTask = $this->getLatestInactiveTask(27);
                    if($existingInactiveTask !== false) {
                        if(strtotime($existingInactiveTask->deadline) >= strtotime($tasksAttributes['deadline'])) {
                            $tasksAttributes['deadline'] = $this->generateDate(['months' => [1, 3, 5, 7, 9, 11], 'days' => [15], 'currentDate'=>$existingInactiveTask->deadline]);
                        }
                    }
                    $tasksList['newTasks'][] = $tasksAttributes;
                }
                else {
                    // Check if frequency is correct AND deadline is in allowed months
                    if( ($activeTask->frequency==$tasksAttributes['frequency']) && $this->deadlineIsAllowed($activeTask->deadline, ['months' => [1, 3, 5, 7, 9, 11], 'days' => [15]]) ) {
                        $tasksList['tasksToBeSkipped'][] = $activeTask;
                    }
                    else {
                        $tasksList['tasksToBeUpdated'][] = $tasksAttributes + ['tmsTaskId'=>$activeTask->id];
                    }
                }
            }

            // ID:   #26
            // Name: Lønn (kunde kjører selv)
            if ($this->contract['salary_check'] == 1) {
                $deadline = $this->generateDate(['months' => [2, 4, 6, 8, 10, 12], 'days' => [15], 'currentDate' => $this->contract['start_date']]);
                $tasksAttributes = ['template' => 26, 'repeating' => true, 'frequency' => '2 months 15', 'deadline' => $deadline];

                // Check if client already has an active task for this template
                $activeTask = $this->getLatestActiveTask(26);
                if($activeTask === false) {
                    // check for latest deadline of completed tasks (completed, re-opened and regenerated)
                    $existingInactiveTask = $this->getLatestInactiveTask(26);
                    if($existingInactiveTask !== false) {
                        if(strtotime($existingInactiveTask->deadline) >= strtotime($tasksAttributes['deadline'])) {
                            $tasksAttributes['deadline'] = $this->generateDate(['months' => range(1,12), 'days' => [15], 'currentDate'=>$existingInactiveTask->deadline]);
                        }
                    }
                    $tasksList['newTasks'][] = $tasksAttributes;
                }
                else {
                    // Check if frequency is correct AND deadline is in allowed months
                    if( ($activeTask->frequency==$tasksAttributes['frequency']) && $this->deadlineIsAllowed($activeTask->deadline, ['months' => range(1,12), 'days' => [15]]) ) {
                        $tasksList['tasksToBeSkipped'][] = $activeTask;
                    }
                    else {
                        $tasksList['tasksToBeUpdated'][] = $tasksAttributes + ['tmsTaskId'=>$activeTask->id];
                    }
                }
            }

            // ID:   #25
            // Name: Lønn
            if ($this->contract['salary'] == 1) {
                $salaryFrequencies = [];
                foreach ($this->contract['salary_day'] as $salaryDay) {
                    if ($salaryDay != 30) { // 30 = 'man' in TMS

                        $deadlineDay = $salaryDay;
                        $deadline = $this->generateDate(['months' => range(1, 12), 'days' => [$deadlineDay], 'currentDate' => $referenceDate]);

                        // deadline is last day of current month if salary day is 'end'
                        if ($deadlineDay == '29') { // 29 = 'end' in TMS
                            $deadlineDay = 'end';
                            $lastDayOfCurrentMonth = date("t");
                            $deadline = date("Y-m-t");  // 't' returns the number of days in the month of a given date
                            // check if we are currently in the last day of the month
                            if (date('j') == $lastDayOfCurrentMonth) {
                                $deadline = date('Y-m-t', strtotime(date("Y-m-t") . ' +1 day')); // add 1 day so we can switch to next month and then calculate the last day of that month
                            }
                        }
                        $frequency = '1 months ' . $deadlineDay;
                        $salaryFrequencies[] = $frequency;
                        // deadline is last day of current month if salary day is 'end'
                        $tasksAttributes = ['template' => 25, 'repeating' => true, 'frequency' => $frequency, 'deadline' => $deadline];

                        $found = false;
                        if(isset($this->existingClientTasks[25])) { // there is at least one task with template 25
                            foreach($this->existingClientTasks[25] as $existingTmsTask) {
                                if(is_null($existingTmsTask->completed_at )) {
                                    if($existingTmsTask->frequency == $frequency) {
                                        $found = $existingTmsTask;
                                        $tasksList['tasksToBeSkipped'][] = $existingTmsTask;
                                        break;
                                    }
                                }
                            }
                            if($found === false) {
                                $tasksList['newTasks'][] = $tasksAttributes;
                            }
                        }
                        else {
                            $tasksList['newTasks'][] = $tasksAttributes;
                        }
                    }
                    else {
                        // ID:   #47 --> for 'man' salary day
                        // Name: Send inn A-melding
                        $deadline = $this->generateDate(['months' => range(1,12), 'days' => [5], 'currentDate' => $this->contract['start_date']]);
                        $tasksAttributes = ['template' => 47, 'repeating' => true, 'frequency' => '1 months 5', 'deadline' => $deadline];

                        // Check if client already has an active task for this template
                        $activeTask = $this->getLatestActiveTask(47);
                        if($activeTask === false) {
                            // check for latest deadline of completed tasks (completed, re-opened and regenerated)
                            $existingInactiveTask = $this->getLatestInactiveTask(47);
                            if($existingInactiveTask !== false) {
                                if(strtotime($existingInactiveTask->deadline) >= strtotime($tasksAttributes['deadline'])) {
                                    $tasksAttributes['deadline'] = $this->generateDate(['months' => range(1,12), 'days' => [5], 'currentDate'=>$existingInactiveTask->deadline]);
                                }
                            }
                            $tasksList['newTasks'][] = $tasksAttributes;
                        }
                        else {
                            // Check if frequency is correct AND deadline is in allowed months
                            if( ($activeTask->frequency==$tasksAttributes['frequency']) && $this->deadlineIsAllowed($activeTask->deadline, ['months' => range(1,12), 'days' => [5]]) ) {
                                $tasksList['tasksToBeSkipped'][] = $activeTask;
                            }
                            else {
                                $tasksList['tasksToBeUpdated'][] = $tasksAttributes + ['tmsTaskId'=>$activeTask->id];
                            }
                        }
                    }
                }
            }

            // ID:   #71  -  special rule so that these tasks should only be applied to new customers
            // Name: Onboard client
            if((int)$this->client->id > 642 ) {
                $deadline = date('Y-m-d', strtotime("+1 days"));
                $tasksAttributes = ['template' => 71, 'repeating' => false, 'frequency' => '', 'deadline' => $deadline];

                $existingTask = $this->clientHasTaskTemplateId( 71);

                if($existingTask === false) {
                    $tasksList['newTasks'][] = $tasksAttributes;
                }
                else {
                    $tasksList['tasksToBeSkipped'][] = $existingTask;
                }
            }
        }

        // Fill in the list with the rest of tasks that need to be deleted.
        // This list is made by client tasks that are not updated or skipped
        $remainingTmsTemplates = array_keys($this->existingClientTasks);

        if(isset($tasksList['tasksToBeUpdated'])) {
            foreach ($tasksList['tasksToBeUpdated'] as $task) {
                if (($key = array_search($task['template'], $remainingTmsTemplates)) !== false) {
                    unset($remainingTmsTemplates[$key]);
                }
            }
        }
        if(isset($tasksList['tasksToBeSkipped'])) {
            foreach ($tasksList['tasksToBeSkipped'] as $task) {
                if (($key = array_search($task->template_id, $remainingTmsTemplates)) !== false) {
                    unset($remainingTmsTemplates[$key]);
                }
            }
        }

        foreach($this->existingClientTasks as $existingClientTemplate => $existingClientTasks) {
            if(in_array($existingClientTemplate, $remainingTmsTemplates)) {
                foreach ($existingClientTasks as $task) {
                    if (is_null($task->completed_at) && $task->reopened == 0 && $task->regenerated == 0) {
                        $tasksList['tasksToBeDeleted'][] = $task;
                    }
                }
            }
        }

        return $tasksList;
    }

    public function generateOneTimeContractTasks()
    {
        $tasksList = [];

        // #35 - Enkelttimer
        $tasksList[] = $this->generateTask35();

        // #77 - On-board client - One time contract
        $tasksList[] = $this->generateTask35();
        $deadline = Carbon::now()->addDays(1);
        $tasksAttributes = ['template' => 77, 'repeating' => false, 'frequency' => '', 'deadline' => $deadline];
        if(false === false) { // IF client doesn't have this template ID
            $tasksList['newTasks'][] = $tasksAttributes;
        }

        return $tasksList;
    }

    public function generateNormalContractTasks()
    {
        return [];
    }

    public function generateUnder50BillsContractTasks()
    {
        return [];
    }

    protected function generateTask35()
    {
        return [
            'template'  => 35,
            'repeating' => Task::NOT_REPEATING,
            'deadline'  => Carbon::now()->addDays(3),
        ];
    }

    protected function generateTask77()
    {
        return [
            'template'  => 77,
            'repeating' => Task::NOT_REPEATING,
            'deadline'  => Carbon::now()->addDays(1),
        ];
    }

    public function getExistingClientTasks()
    {
        $result = [];

        $tasks = $this->client->tasks(false)
            ->leftJoin('task_reopenings', function ($join) {
                $join->on('tasks.id', '=', 'task_reopenings.task_id')->whereNotNull('task_reopenings.completed_at');
            })
            ->whereIn('tasks.template_id', $this->allowedTaskTemplates)
            ->select('tasks.*', DB::raw('IF(task_reopenings.id > 0  , 1, 0) as reopened'))
            ->get();

        // Format result set
        foreach($tasks as $task) {
            $result[$task->template_id][] = $task;
        }

        return $result;
    }

    /**
     * Generate the next appearance of a date AFTER a specific one (minDate)
     *
     * @param array $params
     * @return string
     */
    public function generateDate(array $params = []) {
        if(isset($params['currentDate'])) {
            $minDate = Carbon::parse($params['currentDate'])->format('Y-n-j');
        }
        else {
            $minDate = Carbon::now()->format('Y-n-j');
        }

        $currentYear = Carbon::parse($minDate)->year;

        $availableMonths = isset($params['months']) ? $params['months'] : [];
        $availableDays = isset($params['days']) ? $params['days'] : [];

        sort($availableMonths);
        sort($availableDays);

        $foundDate = false;
        while($foundDate == false) {
            foreach($availableMonths as $month) {
                foreach($availableDays as $day) {
                    if ( strtotime($currentYear.'-'.$month.'-'.$day) > strtotime($minDate)) {
                        $foundDate = true; break 3;
                    }
                }
            }
            $currentYear += 1;
        }

        $day = str_pad($day, 2, 0, STR_PAD_LEFT);
        $month = str_pad($month, 2, 0, STR_PAD_LEFT);

        return Carbon::parse($currentYear.'-'.$month.'-'.$day.'23:59:00');
    }

    protected function clientHasTaskTemplateId($templateId, $options=[])
    {
        if(!isset($options['onlyUncompleted'])) {
            $options['onlyUncompleted'] = false;
        }


        if($options['onlyUncompleted'] == false) {
            if(array_key_exists($templateId, $this->existingClientTasks)){
                return end($this->existingClientTasks[$templateId]);
            }
            else {
                return false;
            }
        }
        else {
            if(isset($this->existingClientTasks[$templateId]) && !empty($this->existingClientTasks[$templateId])) {
                foreach(array_reverse($this->existingClientTasks[$templateId]) as $task) {
                    if(is_null($task->completed_at)) return $task;
                }
            }
            return false;
        }
    }

    protected function getLatestActiveTask($templateId)
    {
        if(!array_key_exists($templateId, $this->existingClientTasks)) return false;
        $returnValue = null;
        foreach($this->existingClientTasks[$templateId] as $task) {
            if(is_null($task->completed_at) && $task->reopened==0 && $task->regenerated==0) {
                if(is_null($returnValue)) {
                    $returnValue = $task;
                }
                else {
                    if(strtotime($task->deadline) > strtotime($returnValue->deadline)) {
                        $returnValue = $task;
                    }
                }
            }
        }
        return is_null($returnValue) ? false : $returnValue;
    }

    protected function getLatestInactiveTask($templateId)
    {
        if(!array_key_exists($templateId, $this->existingClientTasks)) return false;
        $returnValue = null;
        foreach($this->existingClientTasks[$templateId] as $task) {
            if(!is_null($task->completed_at) || $task->reopened==1 || $task->regenerated==1) {
                if(is_null($returnValue)) {
                    $returnValue = $task;
                }
                else {
                    if(strtotime($task->deadline) > strtotime($returnValue->deadline)) {
                        $returnValue = $task;
                    }
                }
            }
        }
        return is_null($returnValue) ? false : $returnValue;
    }

    protected function deadlineIsAllowed($date, $dateParams = [])
    {
        $timestamp = strtotime($date);
        $day = date("d", $timestamp);
        $month = date("m", $timestamp);

        if(in_array($day, $dateParams['days']) && in_array($month, $dateParams['months'])) {
            return true;
        }
        return false;
    }


}