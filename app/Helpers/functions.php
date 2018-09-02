<?php

if (!function_exists('notification')) {
    /**
     * @param string $type
     *
     * @return mixed
     * @throws \Exception
     */
    function notification(string $type) {
        return \App\Services\Notifications\NotificationFactory::make($type);
    }
}

if (!function_exists('getUserWeeklyCapabilityStyleClass')) {

    /**
     * @param \App\Repositories\User\User $user
     * @return null|string
     */
    function getUserWeeklyCapabilityStyleClass(\App\Repositories\User\User $user)
    {
        if ($user->weekly_capacity) {
            return $user->timeEntriesThisWeek() < $user->weekly_capacity ? 'label label-danger' : 'label label-primary';
        }
        return 'label';
    }
}