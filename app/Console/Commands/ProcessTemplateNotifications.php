<?php

namespace App\Console\Commands;

use App\Repositories\Task\Task;
use App\Repositories\Client\Client;
use App\Repositories\Template\Template;
use App\Repositories\TemplateNotification\TemplateNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;

class ProcessTemplateNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'templates:process-notification-templates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the records from template_notifications table and make a record in processed_notifications table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $templates = Template::has('notifications')->with('notifications', 'tasks')->get();

        $successfullyProcessedNotifications = 0;
        $failedProcessedNotifications = 0;
        $alreadyExistedNotifications = 0;

        foreach($templates as $template) {
            foreach($template->notifications as $templateNotification) {
                $template->tasks()->get()->filter(function($task) use ($templateNotification){
                    // First check if notification is due for today, to limit the memory needed for this command.
                    if (Carbon::parse($task->deadline)->subDays((int) $templateNotification->before)->isToday()) {
                        $paidSetting = $this->checkPaidSetting($templateNotification, $task);
                        $deliveredSetting = $this->checkDeliveredSetting($templateNotification, $task);
                        $completedSetting = $this->checkCompletedSetting($templateNotification, $task);
                        $pausedSetting = $this->checkPausedSetting($templateNotification, $task);
                        $clientActiveSetting = $this->checkClientActiveSetting($task);

                        if ($paidSetting && $deliveredSetting && $completedSetting && $pausedSetting && $clientActiveSetting) {
                            return true;
                        }
                        return false;
                    } else { // notification is not due for today
                        return false;
                    }
                })->each(
                /**
                 * @param $task
                 */

                    function ($task) use ($templateNotification, &$successfullyProcessedNotifications, &$failedProcessedNotifications, &$alreadyExistedNotifications) {
                        switch ($templateNotification->type) {
                            case TemplateNotification::TYPE_TEMPLATE:
                                $response = $this->processTemplateTemplateNotification($templateNotification, $task);
                                break;
                            case TemplateNotification::TYPE_EMAIL:
                                $response =  $this->processEmailTemplateNotification($templateNotification, $task);
                                break;
                            case TemplateNotification::TYPE_SMS:
                                $response = $this->processSmsTemplateNotification($templateNotification, $task);
                                break;
                            default:
                                echo "\n \e[31m The template notification with id: ".$templateNotification->id." task id: ".$task->id." failed with error: invalid type for template notification \e[0m \n";
                                $success = false;

                        }

                        if ($response === 1) {
                            $successfullyProcessedNotifications++;
                        } elseif ($response === 0) {
                            $failedProcessedNotifications++;
                        } else {
                            $alreadyExistedNotifications++;
                        }
                    });
            }
        }

        echo "\n Successfully processed notifications: ".$successfullyProcessedNotifications."\n";
        echo "\n Failed processed notifications: ".$failedProcessedNotifications."\n";
        echo "\n Already were processed notifications: ".$alreadyExistedNotifications."\n";
    }

    /**
     * Check completed setting for template notification.
     *
     * @param TemplateNotification $templateNotification
     * @param Task $task
     * @return bool
     */
    protected function checkCompletedSetting(TemplateNotification $templateNotification, Task $task) : bool
    {
        if ($templateNotification->completed == TemplateNotification::BOTH) {
            return true;
        }

        if ($templateNotification->completed == TemplateNotification::TRUE) {
            // Check if task has completed at not equal with null
            if ($task->isComplete()) {
                return true;
            } else {
                return false;
            }
        }

        if ($templateNotification->completed == TemplateNotification::FALSE) {
            // Check if task has completed at equal with null
            if (!$task->isComplete()) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Check paused setting for template notification.
     *
     * @param TemplateNotification $templateNotification
     * @param Task $task
     * @return bool
     */
    protected function checkPausedSetting(TemplateNotification $templateNotification, Task $task) : bool
    {
        if ($templateNotification->paused == TemplateNotification::BOTH) {
            return true;
        }

        if ($templateNotification->paused == TemplateNotification::TRUE) {
            // Check if client is paused
            if ($task->client->paused) {
                return true;
            } else {
                return false;
            }
        }

        if ($templateNotification->paused == TemplateNotification::FALSE) {
            // Check if client is not paused
            if (!$task->client->paused) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Check delivered setting for template notification.
     *
     * @param TemplateNotification $templateNotification
     * @param Task $task
     * @return bool
     */
    protected function checkDeliveredSetting(TemplateNotification $templateNotification, Task $task) : bool
    {
        if ($templateNotification->delivered == TemplateNotification::BOTH) {
            return true;
        }

        if ($templateNotification->delivered == TemplateNotification::TRUE) {
            // Check if task has delivered = 1
            if ($task->delivered == Task::ACTIVE) {
                return true;
            } else {
                return false;
            }
        }

        if ($templateNotification->delivered == TemplateNotification::FALSE) {
            // Check if task has delivered = 0
            if ($task->delivered == Task::NOT_ACTIVE) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Check paid setting for template notification.
     *
     * @param TemplateNotification $templateNotification
     * @param Task $task
     * @return bool
     */
    protected function checkPaidSetting(TemplateNotification $templateNotification, Task $task) : bool
    {
        if ($templateNotification->paid == TemplateNotification::BOTH) {
            return true;
        }

        if ($templateNotification->paid == TemplateNotification::TRUE) {
            // Check if task has active = 1 (that means paid = 1 in clients )
            if ($task->client->paid == Client::IS_PAID) {
                return true;
            } else {
                return false;
            }
        }

        if ($templateNotification->paid == TemplateNotification::FALSE) {
            // Check if task has active = 0 (that means paid = 0 in clients )
            if ($task->client->paid == Client::NOT_PAID) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Check if client is active
     *
     * @param Task $task
     * @return bool
     */
    protected function checkClientActiveSetting(Task $task) : bool
    {
        if ($task->client->active) {
            return true;
        }

        return false;
    }

    /**
     * Process template notification. Compute all notification's variables and save it to approving list.
     * For template notification type template.
     *
     * @param TemplateNotification $templateNotification
     * @param Task $task
     * @return int
     */
    protected function processTemplateTemplateNotification(TemplateNotification $templateNotification, Task $task) : int
    {
        try {
            $data = $this->getDynamicData($task);
            $subject = $templateNotification->details['subject'];

            foreach ($data as $name => $value) {
                $subject = str_replace("[[{$name}]]", $value, $subject);
            }

            $response = notification('template')
                ->template($templateNotification->details['template'])
                ->subject($subject)
                ->to($task->getUserNotificationEmail($templateNotification))
                ->from('')
                ->data(array_merge($templateNotification->details['data'], $data))
                ->saveForApproving($templateNotification->id, $task->client->name, $task->id);

            if (is_null($response)) {
                echo "\n \e[36m The template notification with id: ".$templateNotification->id." task id: ".$task->id." already exists in the list. \e[0m \n";
                return 2;
            } else {
                echo "\n \e[32m The template notification with id: ".$templateNotification->id." task id: ".$task->id." saved to the list to be approved. \e[0m \n";
                return 1;
            }

        } catch (ValidationException $e) {
            echo "\n \e[31m The template notification with id: ".$templateNotification->id." task id: ".$task->id." failed with error: ".$e->getMessage()." \e[0m \n";
            print_r($e->errors());
            echo "\n";
            return 0;
        } catch (\Exception $e) {
            echo "\n \e[31m The template notification with id: ".$templateNotification->id." task id: ".$task->id." failed with error: ".$e->getMessage()." \e[0m \n";
            return 0;
        }
    }

    /**
     * Process template notification. Compute all notification's variables and save it to approving list.
     * For template notification type email.
     *
     * @param TemplateNotification $templateNotification
     * @param Task $task
     * @return int
     */
    protected function processEmailTemplateNotification(TemplateNotification $templateNotification, Task $task) : int
    {
        try {
            $data = $this->getDynamicData($task);
            $message = $templateNotification->details['message'];
            $subject = $templateNotification->details['subject'];

            foreach ($data as $name => $value) {
                $message = str_replace("[[{$name}]]", $value, $message);
                $subject = str_replace("[[{$name}]]", $value, $subject);
            }

            $response = notification('email')
                ->subject($subject)
                ->message($message)
                ->from('')
                ->to($task->getUserNotificationEmail($templateNotification))
                ->saveForApproving($templateNotification->id, $task->client->name, $task->id);

            if (is_null($response)) {
                echo "\n \e[36m The template notification with id: ".$templateNotification->id." task id: ".$task->id." already exists in the list. \e[0m \n";
                return 2;
            } else {
                echo "\n \e[32m The template notification with id: ".$templateNotification->id." task id: ".$task->id." saved to the list to be approved. \e[0m \n";
                return 1;
            }

        } catch (ValidationException $e) {
            echo "\n \e[31m The template notification with id: ".$templateNotification->id." task id: ".$task->id." failed with error: ".$e->getMessage()." \e[0m \n";
            print_r($e->errors());
            echo "\n";
            return 0;
        } catch (\Exception $e) {
            echo "\n \e[31m The template notification with id: ".$templateNotification->id." task id: ".$task->id." failed with error: ".$e->getMessage()." \e[0m \n";
            return 0;
        }
    }

    /**
     * Process template notification. Compute all notification's variables and save it to approving list.
     * For template notification type sms.
     *
     * @param TemplateNotification $templateNotification
     * @param Task $task
     * @return int
     */
    protected function processSmsTemplateNotification(TemplateNotification $templateNotification, Task $task) : int
    {
        if (! $number = $task->getUserNotificationPhone($templateNotification, $task)) {
            echo "\n \e[31m The template notification with id: ".$templateNotification->id." task id: ".$task->id." failed with error: no phone number found. \e[0m \n";
            return 0;
        }
        try {
            $data = $this->getDynamicData($task);
            $message = $templateNotification->details['message'];

            foreach ($data as $name => $value) {
                $message = str_replace("[[{$name}]]", $value, $message);
            }


            $response = notification('sms')
                ->message($message)
                ->to($number)
                ->saveForApproving($templateNotification->id, $task->client->name, $task->id);


            if (is_null($response)) {
                echo "\n \e[36m The template notification with id: ".$templateNotification->id." task id: ".$task->id." already exists in the list. \e[0m \n";
                return 2;
            } else {
                echo "\n \e[32m The template notification with id: ".$templateNotification->id." task id: ".$task->id." saved to the list to be approved. \e[0m \n";
                return 1;
            }
        } catch (ValidationException $e) {
            echo "\n \e[31m The template notification with id: ".$templateNotification->id." task id: ".$task->id." failed with error: ".$e->getMessage()." \e[0m \n";
            print_r($e->errors());
            echo "\n";
            return 0;
        } catch (\Exception $e) {
            echo "\n \e[31m The template notification with id: ".$templateNotification->id." task id: ".$task->id." failed with error: ".$e->getMessage()." \e[0m \n";
            return 0;
        }
    }
    /**
     * @param $task
     *
     * @return array
     */
    protected function getDynamicData($task)
    {
        $deliveredbutton = '<table class="ebtn" align="center" border="0" cellspacing="0" cellpadding="0" style="box-sizing: border-box;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;display: table;margin-left: auto;margin-right: auto;"><tbody><tr><td class="success_b" style="box-sizing: border-box;vertical-align: top;background-color: #32d373;line-height: 20px;font-family: Helvetica, Arial, sans-serif;mso-line-height-rule: exactly;border-radius: 4px;text-align: center;font-weight: bold;font-size: 14px;padding: 8px 16px;"><a href="' . route('tasks.delivered.create.from.hash', encrypt($task->id)) . '" style="text-decoration: none;line-height: inherit;color: #ffffff;"><span style="text-decoration: none;line-height: inherit;color: #ffffff;">Klikk her</span></a></td></tr></tbody></table>';

        return [
            'clientname' => $task->client->name,
            'employeename' => $task->client->employee ? $task->client->employee->name : '',
            'employeepf' => $task->client->employee ? ('PF' . $task->client->employee->pf_id) : '',
            'deadline' => $task->deadline->format('d-m-Y'),
            'taskname' => $task->title,
            'taskdeliveredlink' => $deliveredbutton,
        ];
    }
}
