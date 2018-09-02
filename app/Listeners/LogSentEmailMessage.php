<?php

namespace App\Listeners;

use App\Services\Notifications\EmailNotificationLogger;

class LogSentEmailMessage
{
    /**
     * @var \App\Services\Notifications\EmailNotificationLogger
     */
    protected $logger;

    /**
     * Create the event listener.
     *
     * @param \App\Services\Notifications\EmailNotificationLogger $logger
     */
    public function __construct(EmailNotificationLogger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $this->logger->write([
            array_keys($event->message->getTo()),
            $event->message->getBody()
        ]);
    }
}
