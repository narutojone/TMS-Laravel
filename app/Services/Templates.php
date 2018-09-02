<?php

namespace App\Services;

use App\Repositories\Template\Template;
use Illuminate\Support\Collection;

class Templates
{
    /**
     * @param \App\Repositories\Template\Template $template
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllowedUsers(Template $template) : Collection
    {
        $template->load('groups.users');

        $users = $template->groups->pluck('users')->flatten()->filter(function ($user) {
            return $user->active == 1;
        })->values();

        return $users;
    }

    /**
     * @param \App\Repositories\Template\Template $template
     *
     * @return array
     */
    public function getAllowedUsersIds(Template $template) : array
    {
        return $this->getAllowedUsers($template)->pluck('id')->toArray();
    }
}
