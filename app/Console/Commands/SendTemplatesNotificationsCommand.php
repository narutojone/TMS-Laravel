<?php

namespace App\Console\Commands;

use App\Repositories\ProcessedNotification\ProcessedNotification;
use App\Repositories\ProcessedNotification\ProcessedNotificationInterface;
use App\Repositories\ProcessedNotificationLog\ProcessedNotificationLogInterface;
use App\Repositories\Task\Task;
use App\Repositories\Template\Template;
use App\Repositories\TemplateNotification\TemplateNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SendTemplatesNotificationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'templates:send-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send all notifications for templates.';

    /**
     * MessagesSendCommand constructor.
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

        // Send notifications related to templates
        $processedNotificationsSuccess = 0;
        $processedNotificationsError = 0;

        $processedNotificationRepository = app()->make(ProcessedNotificationInterface::class);
        $processedNotifications = $processedNotificationRepository->model()
            ->where('status', '=', ProcessedNotification::STATUS_APPROVED)
            ->where('sent', '=', ProcessedNotification::IS_NOT_SENT)
            ->get();

        foreach ($processedNotifications as $processedNotification) {
            try {
                if ($processedNotification->templateNotification) {
                    switch ($processedNotification->templateNotification->type) {
                        case 'template':
                            $this->sendTemplateNotification($processedNotification->templateNotification, $processedNotification->task, $processedNotification);
                            break;
                        case 'email':
                            $this->sendEmailNotification($processedNotification->templateNotification, $processedNotification->task, $processedNotification);
                            break;
                        case 'sms':
                            $this->sendSmsNotification($processedNotification->templateNotification, $processedNotification->task, $processedNotification);
                            break;
                    }
                } elseif ($processedNotification->type == ProcessedNotification::CLIENT_UPDATE_EMAIL){
                    $this->sendEmailNotificationForClientUpdate($processedNotification);
                } else {
                    // Send simple sms notifications (not related to templates)
                    $this->sendSimpleSmsNotification($processedNotification);
                }

                $processedNotificationsSuccess++;
            } catch (\Exception $e) {
                $processedNotificationsError++;
                if ($processedNotification->templateNotification && $processedNotification->task) {
                    echo "\n \e[31m Processed notification with id: " . $processedNotification->id . " type: " . $processedNotification->templateNotification->type . " task id: " . $processedNotification->task->id . " failed with error: " . $e->getMessage() . " \e[0m \n";
                } else {
                    echo "\n \e[31m Processed notification with id: " . $processedNotification->id ." failed with error: " . $e->getMessage() . " \e[0m \n";
                }
            }
        }

        echo "\n Processed notifications: ".$processedNotifications->count()." \n";
        echo "\n\e[32m Processed notifications successfully: ".$processedNotificationsSuccess." \e[0m \n";
        echo "\n\e[31m Processed notifications with errors: ".$processedNotificationsError." \e[0m \n\n";


    }

    public function sendEmailNotificationForClientUpdate($processedNotification)
    {
        $data = json_decode($processedNotification->data, true);

        $message = $data['email_template']['content'];
        $subject = $data['email_template']['subject'];
        $data['message'] = $message;

        $fromEmail = $this->getFromEmail($processedNotification);
        $viewData = [];
        foreach ($data['email_template']['viewData'] as $name => $value) {
            $subject = str_replace("[[{$name}]]", $value, $subject);
            $name = str_replace(["[[", "]]"], "", $name);
            $viewData[$name] = $value;
        }

        $sent = notification('template')
            ->template($data['email_template']['template_id'])
            ->subject($subject)
            ->from($fromEmail)
            ->to($data['to'])
            ->data($viewData)
            ->send();

        return $this->processResponse($sent, $processedNotification, null, null);
    }

    /**
     * Send template notification.
     *
     * @param TemplateNotification $notification
     * @param Task $task
     * @param ProcessedNotification $processedNotification
     * @return bool
     * @throws \Exception
     */
    public function sendTemplateNotification(TemplateNotification $notification, Task $task, ProcessedNotification $processedNotification) : bool
    {
        $data = $this->getDynamicData($task);
        $subject = $notification->details['subject'];

        foreach ($data as $name => $value) {
            $subject = str_replace("[[{$name}]]", $value, $subject);
        }

        $fromEmail = $this->getFromEmail($processedNotification);

        $sent = notification('template')
            ->template($notification->details['template'])
            ->subject($subject)
            ->from($fromEmail)
            ->to($task->getUserNotificationEmail($notification))
            ->data(array_merge($notification->details['data'], $data))
            ->send();

        return $this->processResponse($sent, $processedNotification, $notification, $task);
    }

    /**
     * Sent email notification.
     *
     * @param TemplateNotification $notification
     * @param Task $task
     * @param ProcessedNotification $processedNotification
     * @return bool
     * @throws \Exception
     */
    public function sendEmailNotification(TemplateNotification $notification, Task $task, ProcessedNotification $processedNotification) : bool
    {
        $data = $this->getDynamicData($task);
        $message = $notification->details['message'];
        $subject = $notification->details['subject'];

        foreach ($data as $name => $value) {
            $message = str_replace("[[{$name}]]", $value, $message);
            $subject = str_replace("[[{$name}]]", $value, $subject);
        }

        $fromEmail = $this->getFromEmail($processedNotification);

        $sent = notification('email')
            ->subject($subject)
            ->message($message)
            ->from($fromEmail)
            ->to($task->getUserNotificationEmail($notification))
            ->send();

        return $this->processResponse($sent, $processedNotification, $notification, $task);
    }

    public function sendSimpleSmsNotification($processedNotification)
    {
        $data = json_decode($processedNotification->data);

        $sent = notification('sms')
            ->message($data->sms_data->content)
            ->to($data->to)
            ->send();
        return $this->processResponse($sent, $processedNotification, null, null);
    }

    /**
     * Send sms notification.
     *
     * @param TemplateNotification $notification
     * @param Task $task
     * @param ProcessedNotification $processedNotification
     * @return bool
     * @throws \Exception
     */
    public function sendSmsNotification(TemplateNotification $notification, Task $task, ProcessedNotification $processedNotification) : bool
    {
        if (! $number = $task->getUserNotificationPhone($notification)) {
            return false;
        }

        $data = $this->getDynamicData($task);
        $message = $notification->details['message'];

        foreach ($data as $name => $value) {
            $message = str_replace("[[{$name}]]", $value, $message);
        }

        $sent = notification('sms')
            ->message($message)
            ->to($number)
            ->send();

        return $this->processResponse($sent, $processedNotification, $notification, $task);
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

    /**
     * Process response received from the notification class
     *
     * @param bool $sent
     * @param ProcessedNotification $processedNotification
     * @param TemplateNotification $notification
     * @param Task $task
     * @return bool
     * @throws \Exception
     */
    protected function processResponse(bool $sent, ProcessedNotification $processedNotification, ?TemplateNotification $notification, ?Task $task) : bool
    {
        if ($task && $notification) {
            if ($sent) {
                echo "\n \e[32m Processed Notification with id: ".$notification->id." type: ".$notification->type." task id: ".$task->id." sent successfully. \e[0m \n";
            } else {
                echo "\n \e[31m Processed notification with id: ".$notification->id." type: ".$notification->type." task id: ".$task->id." not sent \e[0m \n";
            }
        } else {
            $typeText = "simple sms";
            if ($processedNotification->type) {
                $typeText = "update client action";
            }

            if ($sent) {
                echo "\n \e[32m Processed Notification ".$typeText." with id: ".$processedNotification->id." sent successfully. \e[0m \n";
            } else {
                echo "\n \e[31m Processed notification ".$typeText." with id: ".$processedNotification->id." not sent \e[0m \n";
            }
        }

        $processedNotification->sent = $sent;
        $processedNotification->save();

        $data = $processedNotification->toArray();

        unset($data['id']);
        // Add notification in the processed_notification_logs table (for logs)
        $processedNotificationLogRepository = app()->make(ProcessedNotificationLogInterface::class);
        $processedNotificationLogRepository->create($data);

        // Delete the processed notification record so that we avoid growing the table (processed_notifications) to big
        $processedNotification->delete();

        return $sent;
    }

    /**
     * Return the emailFrom variable
     *
     * @param ProcessedNotification $processedNotification
     * @return null|string
     */
    protected function getFromEmail(ProcessedNotification $processedNotification) : ?string
    {
        $emailFrom = null;
        try {
            $data = json_decode($processedNotification->data);
            if (isset($data->from)) {
                if (is_array($data->from)) {
                    $emailFrom = $data->from[0];
                } else {
                    $emailFrom = $data->from;
                }
            }
        } catch (\Exception $e) {
            $emailFrom = env('MAIL_FROM_ADDRESS', null);
            echo "\n\e[31m Processed notification with id: ".$processedNotification->id. " error: ".$e->getMessage()."\e[0m \n";
            echo "\e[31m Processed notification with id: ".$processedNotification->id. " email from env was added by default: ".$e->getMessage()."\e[0m \n";
        }

        if ($emailFrom == '' || is_null($emailFrom)) {
            $emailFrom = env('MAIL_FROM_ADDRESS', null);
        }

        return $emailFrom;
    }
}
