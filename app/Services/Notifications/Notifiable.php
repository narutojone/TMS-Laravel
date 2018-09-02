<?php

namespace App\Services\Notifications;

use App\Repositories\EmailTemplate\EmailTemplate;
use App\Services\Notifications\Exceptions\MissingNotificationMessageException;
use App\Services\Notifications\Exceptions\MissingNotificationRecipientException;
use App\Services\Notifications\Exceptions\MissingNotificationTemplateException;

trait Notifiable
{
    /**
     * @var mixed
     */
    protected $to = null;

    /**
     * @var mixed
     */
    protected $from = null;

    /**
     * @var null
     */
    protected $content = null;

    /**
     * @var null
     */
    protected $template = null;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $viewData = [];

    /**
     * @var string
     */
    protected $subject = 'Notification from TMS!';

    /**
     * @param mixed $to
     *
     * @return $this
     */
    public function to($to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @param string $from
     * @return $this
     */
    public function from(string $from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function message(string $content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @param string $subject
     *
     * @return $this
     */
    public function subject(string $subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @param string $template
     *
     * @return $this
     * @throws \App\Services\Notifications\Exceptions\MissingNotificationTemplateException
     */
    public function template($template)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        if (! $template = EmailTemplate::find($template)) {
            if (! $template = EmailTemplate::template($template)->first()) {
                throw new MissingNotificationTemplateException;
            }
        }

        $this->template = $template;

        return $this;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function data(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function viewData(array $data)
    {
        $this->viewData = $data;

        return $this;
    }

    /**
     * @throws \App\Services\Notifications\Exceptions\MissingNotificationRecipientException
     */
    protected function guardRecipient()
    {
        if (is_null($this->to)) {
            throw new MissingNotificationRecipientException;
        }
    }

    /**
     * @throws \App\Services\Notifications\Exceptions\MissingNotificationMessageException
     */
    protected function guardMessage()
    {
        if (is_null($this->content)) {
            throw new MissingNotificationMessageException();
        }
    }
}
