<?php

namespace App\Services\Notifications;

use App\Repositories\Client\Client;
use App\Repositories\ContactEmail\ContactEmailInterface;
use App\Repositories\NotifierLog\NotifierLog;
use App\Repositories\User\UserInterface;
use App\Services\Notifications\Contracts\NotificationLoggerInterface;

class EmailNotificationLogger implements NotificationLoggerInterface
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

        foreach ($to as $email) {
            NotifierLog::create(
                array_merge(
                    $this->determineUserTypeByEmail($email),
                    ['to' => $email, 'body' => json_encode($content), 'type' => 'email']
                )
            );
        }
    }

    /**
     * @param $email
     *
     * @return array
     */
    protected function determineUserTypeByEmail($email)
    {
        $contactEmailRepository = app()->make(ContactEmailInterface::class);
        $userRepository = app()->make(UserInterface::class);

        $contactEmail = $contactEmailRepository->model()->where('address', $email)->first();
        if($contactEmail) {
            $client = $contactEmail->contact->clients->first();
            if($client && $client->internal == Client::NOT_INTERNAL) {
                return ['client_id' => $client->id];
            }
        }

        $user = $userRepository->model()->where('email', $email)->first();
        if ($user) {
            return ['user_id' => $user->id];
        }

        return [];
    }
}
