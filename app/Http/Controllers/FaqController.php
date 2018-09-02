<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Faq\FaqInterface;
use App\Repositories\Faq\FaqUpdateRequest;
use App\Repositories\Template\TemplateInterface;
use App\Repositories\FaqCategory\FaqCategoryInterface;
use App\Repositories\Faq\FaqCreateRequest;

class FaqController extends Controller
{
    /**
     * @var $faqRepository - EloquentRepositoryFaq
     */
    private $faqRepository;

     /**
     * @var $templateRepository - EloquentRepositoryTemplate
     */
    private $templateRepository;

    /**
     * @var $faqCategoryRepository - EloquentRepositoryFaqCategory
     */
    private $faqCategoryRepository;

    /**
     * FaqController constructor.
     *
     * @param FaqInterface $faqRepository
     * @param TemplateInterface $templateRepository
     * @param FaqCategoryInterface $faqCategoryRepository
     * @internal param FaqInterface $userRepository
     */
    public function __construct(FaqInterface $faqRepository, TemplateInterface $templateRepository, FaqCategoryInterface $faqCategoryRepository)
    {
        parent::__construct();

        $this->faqRepository = $faqRepository;
        $this->templateRepository = $templateRepository;
        $this->faqCategoryRepository = $faqCategoryRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id - id of the requeste resource
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, int $id)
    {
        $faq = $this->faqRepository->make()->find($id);
        
        return view('faq.show')->with([
            'faq' => $faq,
        ]);
    }

    /**
     * Show the edit form for a Faq entity
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id - the id of the resource to be edited
     * 
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, int $id)
    {
        $faq = $this->faqRepository->make()->find($id);

        $faqCategories = $this->faqCategoryRepository->make()->where('active', 1)->orderBy('order')->get();

        return view('faq.edit')->with([
            'faq'           => $faq,
            'faqCategories' => $faqCategories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Repositories\Faq\FaqUpdateRequest $request
     * @param  int $id - the id of the resource to be updated
     *
     * @return \Illuminate\Http\Response
     */
    public function update(FaqUpdateRequest $request, int $id)
    {
        $faq = $this->faqRepository->update($id, $request->all());

        return redirect()
            ->action('FaqCategoryController@show', $faq->faqCategory)
            ->with('success', 'FAQ updated.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $faqCategory = $request->input('faqCategory'); // Used for setting the default value on category (comes as GET param)
        
        $templates = $this->templateRepository->make()->orderBy('title')->get();
        $faqCategory = $this->faqCategoryRepository->make()->find($faqCategory);
        $faqCategories = $this->faqCategoryRepository->make()->where('active', 1)->orderBy('order', 'ASC')->get();
        
        return view('faq.create')->with([
            'templates'     => $templates,
            'faqCategory'   => $faqCategory,
            'faqCategories' => $faqCategories,
        ]);
    }

    /**
     * Create the specified resource in storage.
     *
     * @param \App\Repositories\Faq\FaqCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(FaqCreateRequest $request)
    {
        $faq = $this->faqRepository->create($request->all());

        return redirect()
            ->action('FaqCategoryController@show', $faq->faqCategory)
            ->with('success', 'FAQ created.');
    }

    /**
     * Delete the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id - id of the resource to be deleted (updated with the appropiate value for active column)
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, int $id)
    {
        $faq = $this->faqRepository->make()->find($id);
        $input = $faq->toArray();

        if ($request->input('active') == 1) {
            $input['active'] = false;
            $message = 'FAQ deactivated.';
        } else {
            $input['active'] = true;
            $message = 'FAQ activated.';
        }

        $faq->fill($input);
        $faq->save();

        return redirect()
            ->action('FaqCategoryController@show', $faq->faqCategory)
            ->with('success', $message);
    }

    /**
     * Move the FAQ up or down.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function move(Request $request, int $id)
    {
        $faq = $this->faqRepository->move($id, $request->input('direction'));

        return back()
            ->with('success', "FAQ {$faq->title} moved {$request->input('direction')}.");
    }
}
