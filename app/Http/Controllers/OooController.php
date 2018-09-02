<?php

namespace App\Http\Controllers;

use App\Repositories\OooReason\OooReasonCreateRequest;
use App\Repositories\OooReason\OooReasonInterface;
use App\Repositories\OooReason\OooReasonUpdateRequest;
use Illuminate\Http\Request;

class OooController extends Controller
{
    /**
     * @var $oooReasonRepository - EloquentRepositoryOooReason
     */
    private $oooReasonRepository;

    /**
     * OooController constructor.
     *
     * @param OooReasonInterface $oooReasonRepository
     */
    public function __construct(OooReasonInterface $oooReasonRepository)
    {
        parent::__construct();

        $this->oooReasonRepository = $oooReasonRepository;
    }

    /**
     * Show a list of ooo reasons
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $reasons = $this->oooReasonRepository->make()->orderBy('name', 'ASC')->get();

        return view('settings.out-of-office.index')->with([
            'reasons' => $reasons,
        ]);
    }

    /**
     * Show the create form for ooo
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('settings.out-of-office.create');
    }

    /**
     * Show the edit form
     *
     * @param Request $request
     * @param int $id
     * @return $this
     */
    public function edit(Request $request, int $id)
    {
        $reason = $this->oooReasonRepository->make()->find($id);

        return view('settings.out-of-office.edit')->with([
            'reason' => $reason,
        ]);
    }

    /**
     * Update a reason
     *
     * @param OooReasonUpdateRequest $request
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(OooReasonUpdateRequest $request, int $id)
    {
        $this->oooReasonRepository->update($id, $request->all());

        return redirect()->route('settings.ooo.index')->with('success', 'Reason updated.');
    }

    /**
     * Create a new Ooo Reason
     *
     * @param OooReasonCreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(OooReasonCreateRequest $request)
    {
        $this->oooReasonRepository->create($request->all());

        return redirect()->route('settings.ooo.index');
    }
}
