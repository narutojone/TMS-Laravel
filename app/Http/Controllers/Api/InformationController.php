<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Information\InformationCreateRequest;
use App\Repositories\Information\InformationInterface;
use App\Repositories\Information\InformationTransformer;
use App\Repositories\Information\InformationUpdateRequest;
use Illuminate\Http\Request;


class InformationController extends Controller
{
    /**
     * @var $informationRepository - EloquentRepositoryInformationUser
     */
    private $informationRepository;

    /**
     * InformationController constructor.
     *
     * @param InformationInterface $informationRepository
     */
    public function __construct(InformationInterface $informationRepository)
    {
        parent::__construct();

        $this->informationRepository = $informationRepository;
    }

    /**
     * Show a list of information resources
     *
     * @param Request $request
     *
     * @return collection
     */
    public function index(Request $request)
    {
        $information = $this->informationRepository->make()->paginate(25);

        return $information;
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

        return (new InformationTransformer)->transform($information);
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

        return (new InformationTransformer)->transform($information);
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

        return (new InformationTransformer)->transform($information);
    }

    /**
     * Delete a Information resource
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     *
     * @return void
     */
    public function destroy(Request $request, int $id)
    {
        $this->informationRepository->delete($id);
    }
}