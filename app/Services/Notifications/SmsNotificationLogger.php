<?php

namespace App\Services\Notifications;

use App\Repositories\Client\Client;
use App\Repositories\ContactPhone\ContactPhoneInterface;
use App\Repositories\NotifierLog\NotifierLog;
use App\Services\Notifications\Contracts\NotificationLoggerInterface;
use App\Repositories\User\User;

class SmsNotificationLogger implements NotificationLoggerInterface
{
    /**
     * @param array $data
     */
    public function write(array $data)
    {
        list($to, $content) = $data;

        if (! is_array($to)) {
            $to = (array) $to;
        }

        foreach ($to as $phone) {
            NotifierLog::create(
                array_merge(
                    $this->determineUserTypeByPhoneNumber($phone),
                    ['to' => $phone, 'body' => $content, 'type' => 'sms']
                )
            );
        }
    }

    /**
     * @param $phoneNumber
     *
     * @return array
     */
    protected function determineUserTypeByPhoneNumber($phoneNumber)
    {
        $client = null;
        $phoneNumber = ltrim($phoneNumber, '+');

        $contactPhoneRepository = app()->make(ContactPhoneInterface::class);
        $contactPhone = $contactPhoneRepository->model()->where('number', $phoneNumber)->first();

        if ($contactPhone) {
            $client = $contactPhone->contact->clients->first();
        }

        if ($client && $client->internal == 0) {
            return ['client_id' => $client->id];
        }

        if ($user = User::wherePhone($phoneNumber)->first()) {
            return ['user_id' => $user->id];
        }

        return [];
    }
}
