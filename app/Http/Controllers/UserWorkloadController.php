<?php

namespace App\Http\Controllers;

use App\Repositories\System\System;
use App\Repositories\User\User;
use App\Repositories\UserWorkload\UserWorkloadInterface;
use App\Repositories\UserWorkload\UserWorkloadUpdateRequest;
use Carbon\Carbon;

class UserWorkloadController extends Controller
{
    /**
     * @var $userWorkloadRepository - EloquentRepositoryUserWorkload
     */
    private $userWorkloadRepository;

    /**
     * UserWorkloadController constructor.
     *
     * @param UserWorkloadInterface $userWorkloadRepository
     */
    public function __construct(UserWorkloadInterface $userWorkloadRepository)
    {
        parent::__construct();

        $this->userWorkloadRepository = $userWorkloadRepository;
    }

    /**
     * @param User $user
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(User $user)
    {
        // Authorize request
//        $this->authorize('edit', System::class);

        $userWorkloadMonths = $this->userWorkloadRepository->getWorkload($user, 12);

        return view('users.workload.edit')->with([
            'user'               => $user,
            'userWorkloadMonths' => $userWorkloadMonths,
        ]);
    }

    /**
     * @param UserWorkloadUpdateRequest $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     * @internal param int $id
     *
     */
    public function update(UserWorkloadUpdateRequest $request, User $user)
    {
        // Authorize request
//        $this->authorize('update', System::class);


        $workload = $request->get('workload', []);
        $locked = $request->get('locked', []);

        $workloadItems = [];

        foreach ($workload as $key => $workloadValue) {
            $workloadItems[$key] = [
                'hours'  => $workloadValue,
                'locked' => $locked[$key],
            ];
        }

        $this->userWorkloadRepository->updateAll($user, $workloadItems);

        return redirect()
            ->route('user.show', $user)
            ->with('success', "Workload updated");
    }

}
