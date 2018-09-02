<?php

namespace App\Console\Commands;

use App\Repositories\ZendeskGroup\ZendeskGroupInterface;
use Carbon\Carbon;
use Huddle\Zendesk\Facades\Zendesk;
use Illuminate\Console\Command;

class UpdateZendeskGroups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zendesk:update-groups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update zendesk groups';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $zenDeskGroupsRepository = app()->make(ZendeskGroupInterface::class);

        $page = 1;
        do {
            $groupsResponse = Zendesk::groups()->findAll(['page' => $page]);
            foreach($groupsResponse->groups as $group) {
                $attributes = [
                    'group_id'   => $group->id,
                    'name'       => $group->name,
                    'url'        => $group->url,
                    'deleted'    => (int)$group->deleted,
                    'created_at' => Carbon::parse($group->created_at)->toDateTimeString(),
                    'updated_at' => Carbon::parse($group->updated_at)->toDateTimeString(),
                ];

                $existingZendeskGroup = $zenDeskGroupsRepository->model()->where('group_id', $group->id)->first();
                if($existingZendeskGroup) {
                    // Update existing group
                    $zenDeskGroupsRepository->update($existingZendeskGroup->id, $attributes);
                }
                else {
                    // Create a new group
                    $zenDeskGroupsRepository->create($attributes);
                }
            }
            $page++;
            $nextPage = $groupsResponse->next_page;
        } while (!is_null($nextPage));
    }
}
