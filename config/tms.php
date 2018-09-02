<?php

return [
    /**
     * Notifiers used in the system
     */
    'notifiers' => [
        'email' => \App\Services\Notifications\EmailNotification::class,
        'template' => \App\Services\Notifications\TemplateNotification::class,
        'sms' => \App\Services\Notifications\SmsNotification::class,
    ],

];
