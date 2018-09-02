<?php

namespace App\Http\Controllers;

use App\Repositories\CustomerType\CustomerType;
use App\Repositories\Option\OptionInterface;
use App\Repositories\System\System;
use App\Repositories\TaskType\TaskType;
use App\Repositories\User\UserInterface;
use App\Repositories\UserOutOutOffice\UserOutOutOffice;
use App\Repositories\UserWorkload\UserWorkloadInterface;
use Illuminate\Http\Request;

class SettingsController extends Controller
{

    private $userRepository;

    private $optionRepository;

    private $userWorkloadRepository;

    /**
     * SettingsController constructor.
     * @param UserInterface $userRepository
     * @param OptionInterface $optionRepository
     * @param UserWorkloadInterface $userWorkloadRepository
     */
    public function __construct(UserInterface $userRepository, OptionInterface $optionRepository, UserWorkloadInterface $userWorkloadRepository)
    {
        parent::__construct();

        $this->userRepository = $userRepository;
        $this->optionRepository = $optionRepository;
        $this->userWorkloadRepository = $userWorkloadRepository;
    }

    /**
     * Show the form for editing the user's settings.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $user = $request->user(); // Currently logged in user

        if(!$user->updated_profile){
            // Set profile updated = true
            $this->userRepository->update($user->id, [
                'updated_profile' => 1,
            ]);
        }

        $groupForYearlyStatements = $this->optionRepository->model()->where('key', '=', 'group_yearly_statements_field')->first()->group;

        // Get workload for a user
        $workloadMonths = $this->userWorkloadRepository->getWorkload($user);

        return view('settings')->with([
            'customerTypes'            => CustomerType::pluck('name', 'id')->toArray(),
            'systems'                  => System::visible()->pluck('name', 'id')->toArray(),
            'taskTypes'                => TaskType::pluck('name', 'id')->toArray(),
            'outOfOffice'              => UserOutOutOffice::with(['reason'])->where('user_id', $request->user()->id)->get(),
            'flags'                    => $request->user()->flags()->orderBy('pivot_created_at', 'desc')->get(),
            'workloadMonths'           => $workloadMonths,
            'groupForYearlyStatements' => $groupForYearlyStatements,
        ]);
    }

    /**
     * Update the settings in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->userRepository->update($request->user()->id, $request->all());

        // Prepare data for updating user workload
        $workloadItems = $request->get('workload', []);
        $workloadDataToBeUpdated = [];
        foreach($workloadItems as $workloadDate => $workloadItem) {
            $workloadDataToBeUpdated[$workloadDate] = [
                'hours'  => $workloadItem,
                'locked' => false,
            ];
        }
        $this->userWorkloadRepository->updateAll($request->user(), $workloadDataToBeUpdated);

        return redirect()
            ->action('SettingsController@edit')
            ->with('success', 'User updated.');
    }
}
