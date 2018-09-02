<?php

namespace App\Http\Controllers;

use App\Repositories\Flag\Flag;
use App\Repositories\Flag\FlagCreateRequest;
use App\Repositories\Flag\FlagInterface;
use App\Repositories\Flag\FlagUpdateRequest;
use Illuminate\Http\Request;

class FlagsController extends Controller
{
    /**
     * @var $flagRepository - EloquentRepositoryFlag
     */
    private $flagRepository;

    /**
     * FlagsController constructor.
     *
     * @param FlagInterface $flagRepository
     */
    public function __construct(FlagInterface $flagRepository)
    {
        $this->middleware('admin_only');
        parent::__construct();

        $this->flagRepository = $flagRepository;
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $flags = $this->flagRepository->make()->paginate(25);

        return view('flags.index')->with([
            'flags' => $flags,
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('flags.create');
    }

    /**
     * @param FlagCreateRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(FlagCreateRequest $request)
    {
        $flag = $this->flagRepository->create($request->all());

        return redirect()
            ->route('settings.flags.index')
            ->with('success', 'Flag with reason `' . $flag->reason . '` was created successfully.');
    }

    /**
     * @param \App\Repositories\Flag\Flag $flag
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Flag $flag)
    {
        return view('flags.edit')->with([
            'flag' => $flag,
        ]);
    }

    /**
     * @param FlagUpdateRequest $request
     * @param int $id
     * @return Flag|\Illuminate\Http\RedirectResponse
     */
    public function update(FlagUpdateRequest $request, int $id)
    {
        $flag = $this->flagRepository->update($id, $request->all());

        return redirect()
            ->route('settings.flags.index')
            ->with('success', 'Flag with reason `' . $flag->reason . '` was updated successfully.');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, int $id)
    {
        $reason = $this->flagRepository->make()->find($id)->reason;

        $this->flagRepository->delete($id);

        return redirect()
            ->route('settings.flags.index')
            ->with('success', 'Flag with reason `' . $reason . '` was deleted successfully.');
    }
}
