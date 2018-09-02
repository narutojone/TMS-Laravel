<?php

namespace App\Services\Notifications;

class NotificationFactory
{
    /**
     * @param string $type
     *
     * @return mixed
     * @throws \Exception
     */
    public static function make(string $type)
    {
        $notifiers = config('tms.notifiers');

        if (! isset($notifiers[$type])) {
            throw new \Exception("Notifier {$type} is not registered. Each notifier should be registered in tms.php config file.");
        }

        return app()->make($notifiers[$type]);
    }
}
