<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Faq\FaqInterface;
use App\Repositories\Faq\FaqTransformer;
use App\Repositories\Faq\FaqUpdateRequest;
use App\Repositories\Faq\FaqCreateRequest;


class FaqController extends Controller
{
    /**
     * @var $faqRepository - EloquentRepositoryFaqCategory
     */
    private $faqRepository;
    
    /**
    * FaqController constructor.
    *
    * @param FaqInterface $faqRepository
    */
    public function __construct(FaqInterface $faqRepository)
    {
        parent::__construct();

        $this->faqRepository = $faqRepository;
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
        
        return (new FaqTransformer)->transform($faq);
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

        return (new FaqTransformer)->transform($faq);
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

        return (new FaqTransformer)->transform($faq);
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

        return (new FaqTransformer)->transform($faq);
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

        return (new FaqTransformer)->transform($faq);
    }

}
