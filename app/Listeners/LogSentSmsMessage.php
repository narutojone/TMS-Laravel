<?php

namespace App\Listeners;

use App\Services\Notifications\SmsNotificationLogger;

class LogSentSmsMessage
{
    /**
     * @var \App\Services\Notifications\SmsNotificationLogger
     */
    protected $logger;

    /**
     * Create the event listener.
     *
     * @param \App\Services\Notifications\SmsNotificationLogger $logger
     */
    public function __construct(SmsNotificationLogger $logger)
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
        $this->logger->write([$event->to, $event->message]);
    }
}
