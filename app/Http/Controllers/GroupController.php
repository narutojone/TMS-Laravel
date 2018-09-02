<?php

namespace App\Http\Controllers;

use App\Repositories\Group\Group;
use App\Repositories\Group\GroupInterface;
use App\Repositories\Group\GroupUpdateReviewersRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * @return void
     */
    public function boot()
    {
        $this->middleware('admin_only');
    }

    /**
     * @var $templateRepository - EloquentRepositoryGroup
     */
    private $groupRepository;

    /**
     * Instantiate a new controller instance.
     *
     * @param GroupInterface $groupRepository
     */
    public function __construct(GroupInterface $groupRepository)
    {
        parent::__construct();

        $this->groupRepository = $groupRepository;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('groups.index', ['groups' => Group::paginate(30)]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('groups.create');
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        Group::create($data = $request->validate(['name' => 'required']));

        return redirect()
            ->route('groups.index')
            ->with('success', "Group named \"{$data['name']}\" has been created successfully.");
    }

    /**
     * @param \App\Repositories\Group\Group $group
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Group $group)
    {
        $group->load('users');

        return view('groups.show', compact('group'));
    }

    /**
     * @param \App\Repositories\Group\Group $group
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Group $group)
    {
        return view('groups.edit', compact('group'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Repositories\Group\Group $group
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Group $group)
    {
        $group->update($data = $request->validate(['name' => 'required']));

        return redirect()
            ->route('groups.index')
            ->with('success', "Group named \"{$data['name']}\" has been updated successfully.");
    }

    /**
     * @param \App\Repositories\Group\Group $group
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Group $group)
    {
        if ($group->templates()->count()) {
            return back()
                ->with('info', "Can't delete template group. Templates are assigned.");
        }

        $group->delete();

        return redirect()
            ->route('groups.index')
            ->with('success', "Group {$group->name} has been deleted successfully.");
    }
}
