<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\FaqCategory\FaqCategoryTransformer;
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id - id of the requested resource
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, int $id)
    {
        $faqCategory = $this->faqCategoryRepository->make()->find($id);
        
        return (new FaqCategoryTransformer)->transform($faqCategory);
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

        return (new FaqCategoryTransformer)->transform($faqCategory);
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

        return (new FaqCategoryTransformer)->transform($faqCategory);
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

        return (new FaqCategoryTransformer)->transform($faqCategory);
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

        return (new FaqCategoryTransformer)->transform($faqCategory);
    }
}
