<?php

namespace App\Services\Notifications;

use App\Repositories\EmailTemplate\EmailTemplate;
use App\Repositories\GeneratedProcessedNotification\GeneratedProcessedNotificationInterface;
use App\Repositories\ProcessedNotification\ProcessedNotification;
use App\Repositories\ProcessedNotification\ProcessedNotificationInterface;
use App\Repositories\TemplateNotification\TemplateNotificationInterface;
use App\Services\Notifications\Contracts\NotifierInterface;
use Illuminate\Mail\Mailer;

class TemplateNotification implements NotifierInterface
{
    use Notifiable;

    /**
     * @var \Illuminate\Mail\Mailer
     */
    protected $mailer;

    /**
     * @var string
     */
    protected $mailable = '\App\Mail\TemplateMailable';

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
     * @throws \Exception
     */
    public function saveForApproving(?int $templateNotificationId, string $clientName = '', ?int $taskId) : ?ProcessedNotification
    {
        $this->guardRecipient();
        $this->fillTemplateWithData();

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
                    'title'         => $this->template->title,
                    'content'       => $this->template->content_html,
                    'footer'        => $this->template->footer_html,
                    'view'          => $this->template->template_file,
                    'subject'       => $this->subject,
                    'viewData'      => $this->data,
                ],
            ],
            'template_notification_id'  => $templateNotificationId,
            'task_id'                   => $taskId,
            'type'                      => $processedNotificationType,
        ];

        if (isset($this->data['[[template_id]]'])) {
            $data['data']['email_template']['template_id'] = $this->data['[[template_id]]'];
        }

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
     * @throws \Exception
     */
    public function send() : bool
    {
        $this->guardRecipient();
        $this->fillTemplateWithData();

        try {
            /** @noinspection PhpUndefinedMethodInspection */

            if (isset($this->data['message']) && !empty($this->data['message'])) {
                $this->mailer->to($this->to)->send(
                    (new $this->mailable($this->template, $this->subject, $this->viewData, $this->from, $this->data['message']))->onQueue('emails')
                );
            } else {
                $this->mailer->to($this->to)->send(
                    (new $this->mailable($this->template, $this->subject, $this->viewData, $this->from))->onQueue('emails')
                );
            }
        } catch (\Exception $e) {
            app('log')->error($e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function fillTemplateWithData() : void
    {
        if (! $this->template instanceof EmailTemplate) {
            throw new \Exception('Email template should be instance of ' . EmailTemplate::class);
        }

        if (count($this->data)) {
            $this->formatKeys();
            $this->replacePlaceholders();
        }

        $this->removeEmptyTags();
    }

    /**
     * @return void
     */
    protected function formatKeys() : void
    {
        $formatted = [];
        foreach ($this->data as $key => $value) {
            $formatted["[[{$key}]]"] = $value;
        }

        $this->data = $formatted;
    }

    /**
     * return void
     */
    protected function replacePlaceholders() : void
    {
        $subjects = ['title', 'content_html', 'footer_html'];

        foreach ($subjects as $subject) {
            try {
                $this->template->{$subject} = str_replace(
                    array_keys($this->data), array_values($this->data), $this->template->{$subject}
                );
            } catch (\Exception $e) {
                $this->template->{$subject} = str_replace(
                    array_keys($this->data), array_values($this->data['[[email_template]]']), $this->template->{$subject}
                );
            }
        }
    }

    /**
     * return void
     */
    protected function removeEmptyTags() : void
    {
        $subjects = ['title', 'content_html', 'footer_html'];
        foreach ($subjects as $subject) {
            $this->template->{$subject} = preg_replace(
                ['#\[\[\w+\]\]#', '#<\w+><\w+><\/\w+>#'], '', $this->template->{$subject}
            );
        }
    }
}
