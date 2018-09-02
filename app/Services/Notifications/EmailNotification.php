<?php

namespace App\Services\Notifications;

use App\Repositories\GeneratedProcessedNotification\GeneratedProcessedNotificationInterface;
use App\Repositories\ProcessedNotification\ProcessedNotification;
use App\Repositories\ProcessedNotification\ProcessedNotificationInterface;
use App\Repositories\TemplateNotification\TemplateNotificationInterface;
use App\Services\Notifications\Contracts\NotifierInterface;
use Illuminate\Mail\Mailer;

class EmailNotification implements NotifierInterface
{
    use Notifiable;

    /**
     * @var \Illuminate\Mail\Mailer
     */
    protected $mailer;

    /**
     * @var string
     */
    protected $mailable = '\App\Mail\DefaultMailable';

    /**
     * EmailNotifier constructor.
     *
     * @param \Illuminate\Mail\Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Save the data about a notification to database for review and approval.
     *
     * @param int $templateNotificationId
     * @param string $clientName
     * @param int $taskId
     * @return ProcessedNotification
     * @throws Exceptions\MissingNotificationRecipientException
     */
    public function saveForApproving(?int $templateNotificationId, string $clientName = '', ?int $taskId) : ?ProcessedNotification
    {
        $this->guardRecipient();

        $processedNotificationType = "";
        if (is_null($templateNotificationId) && is_null($taskId)) {
            $processedNotificationType = ProcessedNotification::CLIENT_UPDATE_EMAIL;
        }


        $data = [
            'data' => [
                'clientName'        => $clientName,
                'to'                => $this->to,
                'from'              => $this->from,
                'email_template'    => [
                    'subject'   => $this->subject,
                    'content'   => $this->content,
                ],
            ],
            'template_notification_id'  => $templateNotificationId,
            'task_id'                   => $taskId,
            'type'                      => $processedNotificationType,
        ];

        // If both these values are null, than the notification it is created from the client update action (if paid was changed or active was chanced....etc.)
        // We consider that the notification was not processed (not the best scenario)
        if (is_null($templateNotificationId) && is_null($taskId)) {
            $notificationWasProcessed = false;
        } else {
            $templateNotificationRepository = app()->make(TemplateNotificationInterface::class);
            $notificationWasProcessed = $templateNotificationRepository->checkIfNotificationWasProcessed($taskId, $templateNotificationId);
        }

        $processedNotificationRepository = app()->make(ProcessedNotificationInterface::class);
        if (!$notificationWasProcessed) { // template was not processed and a notification for it does not exists

            // Create a record in generated_processed_notifications table.
            // This is because we want to make a where query in a table that way smaller
            $generatedTemplateNotificationRepository = app()->make(GeneratedProcessedNotificationInterface::class);
            $generatedTemplateNotificationRepository->create($data);

            // Create a record in processed notifications also, here we approve/decline
            $processedNotification = $processedNotificationRepository->create($data);
            return $processedNotification;
        }

        return null;
    }

    /**
     * @return bool
     * @throws Exceptions\MissingNotificationRecipientException
     */
    public function send()
    {
        $this->guardRecipient();

        try {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->mailer->to($this->to)->send(
                (new $this->mailable($this->content, $this->subject, $this->viewData, $this->from))->onQueue('emails')
            );
        } catch (\Exception $e) {
            app('log')->error($e->getMessage());
            return false;
        }

        return true;
    }
}
