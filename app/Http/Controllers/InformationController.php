<?php

namespace App\Http\Controllers;

use App\Repositories\Information\Information;
use App\Repositories\Information\InformationCreateRequest;
use App\Repositories\Information\InformationInterface;
use App\Repositories\Information\InformationUpdateRequest;
use Illuminate\Http\Request;

class InformationController extends Controller
{
    /**
     * @var $userRepository - EloquentRepositoryUser
     */
    private $informationRepository;

    /**
     * InformationController constructor.
     *
     * @param InformationInterface $informationRepository
     */
    public function __construct(InformationInterface $informationRepository)
    {
        $this->middleware('admin_only');
        parent::__construct();

        $this->informationRepository = $informationRepository;
    }

    /**
     * Show a list of information resources
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        $information = $this->informationRepository->make()->orderBy('id', 'DESC')->paginate(25);

        return view('information.index')->with([
            'information' => $information,
        ]);
    }

    /**
     * Show the information create form
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('information.create');
    }

    /**
     * Store a information resource into the database
     *
     * @param InformationCreateRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(InformationCreateRequest $request)
    {
        $information = $this->informationRepository->create($request->all());

        return redirect()
            ->route('settings.information.index')
            ->with('success', 'Users information #"' . $information->id .'" created.');
    }

    /**
     * Show a resource
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id - id of the requested resource
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, int $id)
    {
        $information = $this->informationRepository->make()->find($id);

        return view('information.show')->with([
            'information' => $information,
        ]);
    }

    /**
     * Show the edit form for a information resource
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, int $id)
    {
        $information = $this->informationRepository->make()->find($id);

        return view('information.edit')->with([
            'information' => $information,
        ]);
    }

    /**
     * Update a Information resource
     *
     * @param InformationUpdateRequest $request
     * @param int $id
     *
     * @return \App\Repositories\Information\Information|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function update(InformationUpdateRequest $request, int $id)
    {
        $information = $this->informationRepository->update($id, $request->all());

        return redirect()
            ->route('settings.information.show', compact('information'))
            ->with('success', "User information #{$information->id} was edited successfully.");
    }

    /**
     * Delete a Information resource
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, int $id)
    {
        $this->informationRepository->delete($id);

        return redirect()
            ->route('settings.information.index')
            ->with('info', 'Users information deleted.');
    }
}
