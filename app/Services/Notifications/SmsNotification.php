<?php

namespace App\Services\Notifications;

use App\Jobs\SendTwilioMessage;
use App\Repositories\GeneratedProcessedNotification\GeneratedProcessedNotificationInterface;
use App\Repositories\ProcessedNotification\ProcessedNotification;
use App\Repositories\ProcessedNotification\ProcessedNotificationInterface;
use App\Repositories\TemplateNotification\TemplateNotificationInterface;
use App\Services\Notifications\Contracts\NotifierInterface;

class SmsNotification implements NotifierInterface
{
    use Notifiable;

    /**
     * @return bool
     * @throws Exceptions\MissingNotificationMessageException
     * @throws Exceptions\MissingNotificationRecipientException
     */
    public function send()
    {
        $this->guardRecipient();
        $this->guardMessage();

        if (! is_array($this->to)) {
            $this->to = [$this->to];
        }

        try {
            foreach ($this->to as $phone) {
                if (! starts_with($phone, '+')) {
                    $phone = '+' . $phone;
                }

                dispatch(
                    (new SendTwilioMessage($phone, $this->content, $this->data))->onQueue('sms')
                );
            }
        } catch (\Exception $e) {
            app('log')->error($e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * Save the data about a notification to database for review and approval.
     *
     * @param int $templateNotificationId
     * @param string $clientName
     * @param int $taskId
     * @return ProcessedNotification
     * @throws Exceptions\MissingNotificationMessageException
     * @throws Exceptions\MissingNotificationRecipientException
     */
    public function saveForApproving(int $templateNotificationId, string $clientName = '', int $taskId) : ?ProcessedNotification
    {
        $this->guardRecipient();
        $this->guardMessage();

        $phoneNumberList = $this->getPhoneNumbersList();

        $data = [
            'data' => [
                'clientName'    => $clientName,
                'to'            => $this->to,
                'sms_data'      => [
                    'phones'    => $phoneNumberList,
                    'content'   => $this->content,
                    'data'      => $this->data,
                ],
            ],
            'template_notification_id'  => $templateNotificationId,
            'task_id'                   => $taskId,
        ];

        $templateNotificationRepository = app()->make(TemplateNotificationInterface::class);
        $notificationWasProcessed = $templateNotificationRepository->checkIfNotificationWasProcessed($taskId, $templateNotificationId);

        if (!$notificationWasProcessed) { // template was not processed and a notification for it does not exists

            // Create a record in generated_processed_notifications table.
            // This is because we want to make a where query in a table that is way smaller
            $generatedTemplateNotificationRepository = app()->make(GeneratedProcessedNotificationInterface::class);
            $generatedTemplateNotificationRepository->create($data);

            // Create a record in processed notifications also, here we approve/decline
            $processedNotificationRepository = app()->make(ProcessedNotificationInterface::class);
            $processedNotification = $processedNotificationRepository->create($data);
            return $processedNotification;
        }

        return null;
    }


    public function saveSimpleSmsForApproving()
    {
        $this->guardRecipient();
        $this->guardMessage();

        $phoneNumberList = $this->getPhoneNumbersList();

        $data = [
            'data' => [
                'to'            => $this->to,
                'phones'        => $phoneNumberList,
                'sms_data'      => [
                    'content'   => $this->content,
                ],
            ],
        ];

        // Create a record in generated_processed_notifications table.
        // This is because we want to make a where query in a table that is way smaller
        $generatedTemplateNotificationRepository = app()->make(GeneratedProcessedNotificationInterface::class);
        $generatedTemplateNotificationRepository->create($data);

        // Create a record in processed notifications also, here we approve/decline
        $processedNotificationRepository = app()->make(ProcessedNotificationInterface::class);
        $processedNotification = $processedNotificationRepository->create($data);
        return $processedNotification;
    }

    /**
     * Return a list of phone numbers
     *
     * @return string
     */
    protected function getPhoneNumbersList() : string
    {
        if (! is_array($this->to)) {
            $this->to = [$this->to];
        }

        // Build the phone number list.
        $phoneNumberList = '';
        foreach ($this->to as $phone) {
            if (! starts_with($phone, '+')) {
                $phone = '+' . $phone;
            }
            $phoneNumberList .= $phone.';';
        }
        $phoneNumberList = rtrim($phoneNumberList, ';');

        return $phoneNumberList;
    }
}
