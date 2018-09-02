<?php

namespace App\Http\Controllers;

use App\Repositories\RatingTemplate\RatingTemplateInterface;
use App\Repositories\RatingTemplate\RatingTemplateCreateRequest;
use App\Repositories\RatingTemplate\RatingTemplateUpdateRequest;

class RatingTemplateController extends Controller
{
    /**
     * @var $ratingTemplateRepository - EloquentRepositoryRatingTemplate
     */
    private $ratingTemplateRepository;
    
    /**
    * RatingTemplateController constructor.
    *
    * @param RatingTemplateInterface $userRepository
    */
    public function __construct(RatingTemplateInterface $ratingTemplateRepository)
    {
        parent::__construct();

        $this->ratingTemplateRepository = $ratingTemplateRepository;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $templates = $this->ratingTemplateRepository->make()->paginate(25);

        return view('settings.rating-templates.index', [
            'templates' => $templates,
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('settings.rating-templates.create');
    }

    /**
     * @param RatingTemplateCreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(RatingTemplateCreateRequest $request)
    {
        $this->ratingTemplateRepository->create($request->all());

        return redirect()
            ->route('rating_templates.index')
            ->with('success', 'Rating has been created successfully.');
    }

    /**
     * @param int $id id of the resource
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $ratingTemplate = $this->ratingTemplateRepository->make()->find($id);

        return view('settings.rating-templates.edit')->with([
            'ratingTemplate' => $ratingTemplate,
        ]);
    }

    /**
     * @param RatingTemplateUpdateRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(RatingTemplateUpdateRequest $request, int $id)
    {
        $ratingTemplate = $this->ratingTemplateRepository->update($id, $request->all());

        return redirect()
            ->route('rating_templates.show', $ratingTemplate)
            ->with('success', 'Rating has been updated successfully.');
    }

    /**
     * @param int $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(int $id)
    {
        $ratingTemplate = $this->ratingTemplateRepository->make()->find($id);

        return view('settings.rating-templates.show')->with([
            'ratingTemplate' => $ratingTemplate,
        ]);
    }

    /**
     * @param int $id - id of the resource to be deleted
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id)
    {
        $this->ratingTemplateRepository->delete($id);

        return redirect()
            ->route('rating_templates.index')
            ->with('success', 'Rating has been deleted successfully.');
    }
}
