<?php

namespace App\Events;

class SmsMessageWasSent
{
    /**
     * @var
     */
    public $to;

    /**
     * @var
     */
    public $message;

    /**
     * Create a new event instance.
     *
     * @param $to
     * @param $message
     */
    public function __construct($to, $message)
    {
        $this->to = $to;
        $this->message = $message;
    }
}
