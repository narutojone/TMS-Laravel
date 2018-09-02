<?php

namespace App\Http\Controllers;

use App\Repositories\Faq\Faq;
use App\Repositories\FaqCategory\FaqCategory;
use App\Repositories\User\User;
use Illuminate\Http\Request;
use App\Repositories\FaqCategory\FaqCategoryInterface;
use App\Repositories\FaqCategory\FaqCategoryCreateRequest;
use App\Repositories\FaqCategory\FaqCategoryUpdateRequest;
use App\Repositories\FaqCategory\FaqCategoryMoveRequest;

class FaqCategoryController extends Controller
{
    /**
     * @var $faqCategoryRepository - EloquentRepositoryFaqCategory
     */
    private $faqCategoryRepository;
    
    /**
    * FaqCategoryController constructor.
    *
    * @param FaqCategoryInterface $faqCategoryRepository
    */
    public function __construct(FaqCategoryInterface $faqCategoryRepository)
    {
        parent::__construct();

        $this->faqCategoryRepository = $faqCategoryRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $faqCategories = $this->faqCategoryRepository->make()->orderBy('order')->get();

        return view('faq.categories.index')->withCategories(
            $faqCategories
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id - id of the requested resource
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, int $id)
    {
        $faqCategory = $this->faqCategoryRepository->make()->find($id);

        $faq = $faqCategory->faq()->orderBy('order');

        if ($request->user()->hasRole(User::ROLE_EMPLOYEE)) {
            $faq->where('visible', 1)->where('active', 1);
        }

        return view('faq.categories.show')
            ->withFaqs($faq->paginate(25))
            ->withFaqCategory($faqCategory);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('faq.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param \App\Repositories\FaqCategory\FaqCategoryCreateRequest $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function store(FaqCategoryCreateRequest $request)
    {
        $faqCategory = $this->faqCategoryRepository->create($request->all());

        return redirect()
            ->action('FaqCategoryController@index')
            ->with('success', 'FAQ category created.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id - id of the requested resource
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        $faqCategory = $this->faqCategoryRepository->make()->find($id);

        return view('faq.categories.edit')->withFaqCategory($faqCategory);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Repositories\FaqCategory\FaqCategoryUpdateRequest $request
     * @param  int $id - id of the resource
     *
     * @return \Illuminate\Http\Response
     */
    public function update(FaqCategoryUpdateRequest $request, int $id)
    {
        $faqCategory = $this->faqCategoryRepository->update($id, $request->all());

        return redirect()
            ->action('FaqCategoryController@index')
            ->with('success', 'FAQ category updated.');
    }

    /**
     * Move the FAQ category up or down.
     *
     * @param \App\Repositories\FaqCategory\FaqCategoryMoveRequest $request
     * @param  int $id -  id of the resource to be moved
     *
     * @return \Illuminate\Http\Response
     */
    public function move(FaqCategoryMoveRequest $request, int $id)
    {
        $faqCategory = $this->faqCategoryRepository->move($id, $request->input('direction'));

        return redirect()
            ->action('FaqCategoryController@index')
            ->with('success', 'FAQ category moved ' . $request->input('direction') . '.');
    }

    /**
     * Delete the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id - id of the resource to be delete (deleted means update with the appropiate value for active column)
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, int $id)
    {
        $faqCategory = $this->faqCategoryRepository->make()->find($id);
        $input = $faqCategory->toArray();
        
        if ($request->input('active') == 1) {
            $input['active'] = false;
            $message = 'Faq category deactivated.';
        } else {
            $input['active'] = true;
            $message = 'Faq category activated.';
        }

        $faqCategory = $this->faqCategoryRepository->update($id, $input);

        return redirect()
            ->action('FaqCategoryController@index')
            ->with('success', $message);
    }
}
