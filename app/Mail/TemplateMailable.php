<?php

namespace App\Mail;

use App\Repositories\EmailTemplate\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TemplateMailable extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var
     */
    public $title;

    /**
     * @var
     */
    public $content;

    /**
     * @var
     */
    public $footer;

    /**
     * @var
     */
    public $subject;

    /**
     * @var string
     */
    public $view;

    /**
     * @var array
     */
    public $viewData;

    public $fromAddres;

    /**
     * Create a new message instance.
     *
     * @param \App\Repositories\EmailTemplate\EmailTemplate $emailTemplate
     * @param $subject
     * @param array $viewData
     */
    public function __construct(EmailTemplate $emailTemplate, $subject, array $viewData = [], $from, $message = null)
    {
        $this->title = $emailTemplate->title;
        if (!is_null($message)) {
            $this->content = $message;
        } else {
            $this->content = $emailTemplate->content_html;
        }

        $this->footer = $emailTemplate->footer_html;
        $this->view = $emailTemplate->template_file;

        $this->subject = $subject;
        $this->viewData = $viewData;
        $this->fromAddres = $from;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)->from($this->fromAddres)->view('layouts.emails.' . $this->view);
    }
}
