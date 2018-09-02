<?php

namespace App\Http\Controllers;

use App\Repositories\GroupUser\GroupUserInterface;
use App\Repositories\Group\GroupInterface;
use App\Repositories\User\UserInterface;
use App\Repositories\GroupUser\GroupUserCreateRequest;

class GroupUserController extends Controller
{
    /**
     * @var $groupUserRepository - EloquentRepositoryGroupUser
     */
    private $groupUserRepository;
    
    /**
     * @var $groupRepository - EloquentRepositoryGroup
     */
    private $groupRepository;

    /**
     * @var $userRepository - EloquentRepositoryUser
     */
    private $userRepository;

    /**
    * GroupUserController constructor.
    *
    * @param GroupUserInterface $groupUserRepository
    */
    public function __construct(GroupUserInterface $groupUserRepository, GroupInterface $groupRepository, UserInterface $userRepository)
    {
        parent::__construct();

        $this->groupUserRepository = $groupUserRepository;
        $this->groupRepository = $groupRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param int $groupId
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(int $groupId)
    {
        $group = $this->groupRepository->make()->find($groupId);
        $users = $this->userRepository->getValidUsersForGroupAdd($groupId);    
        
        return view('groups.user')->with([
            'group' => $group,
            'users' => $users,
        ]);
    }

    /**
     * Assign a user to a group
     * @param GroupUserCreateRequest $request
     * @param int $id - group id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(GroupUserCreateRequest $request, int $id)
    {
        $user = $request->input('user');
        
        $group = $this->groupRepository->make()->find($id);
        $group->users()->attach($user);

        return redirect()
            ->route('groups.show', $group)
            ->with('success', 'New user has been assigned.');
    }

    /**
     * Remove a user from a group
     * @param int $groupId
     * @param int $userId
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $groupId, int $userId)
    {
        $group = $this->groupRepository->make()->find($groupId);
        $user = $this->userRepository->make()->find($userId);

        $group->users()->detach($user);

        return redirect()
            ->route('groups.show', $group)
            ->with('success', "User '{$user->name}' has been removed successfully.");
    }
}
