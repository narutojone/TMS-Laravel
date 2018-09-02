<?php

namespace App\Http\Controllers;

use App\Repositories\DocumentationPage\DocumentationPage;
use App\Repositories\DocumentationPage\DocumentationPageCreateRequest;
use App\Repositories\DocumentationPage\DocumentationPageInterface;
use App\Repositories\DocumentationPage\DocumentationPageUpdateRequest;
use Illuminate\Http\Request;

/**
 * @property DocumentationPageInterface documentationPageRepository
 */
class DocumentationPagesController extends Controller
{
    /**
     * DocumentationPagesController constructor.
     * @param DocumentationPageInterface $documentationPageRepository
     */
    public function __construct(DocumentationPageInterface $documentationPageRepository)
    {
        parent::__construct();

        $this->documentationPageRepository = $documentationPageRepository;
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $documentationPages = $this->documentationPageRepository->model()->orderBy('order', 'ASC')->get();

        return view('documentation-pages.index')->with([
            'documentationPages' => $documentationPages,
        ]);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $parentPageDropdown = $this->documentationPageRepository->getPagesForParentPageDropdown();

        return view('documentation-pages.create')->with([
            'parentPageDropdown' => $parentPageDropdown,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Repositories\DocumentationPage\DocumentationPageCreateRequest
     *
     * @return \Illuminate\Http\Response
     */
    public function store(DocumentationPageCreateRequest $request)
    {
        $documentationPage = $this->documentationPageRepository->create($request->all());

        return redirect()
            ->action('DocumentationPagesController@edit', $documentationPage)
            ->with('success', 'Documentation Page created');
    }

    /**
     * @param DocumentationPage $documentationPage
     * @return \Illuminate\Http\Response
     */
    public function edit(DocumentationPage $documentationPage)
    {
        $parentPageDropdown = $this->documentationPageRepository->getPagesForParentPageDropdown($documentationPage->id);

        return view('documentation-pages.edit')->with([
            'documentationPage'  => $documentationPage,
            'parentPageDropdown' => $parentPageDropdown,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param DocumentationPageUpdateRequest $request
     * @param DocumentationPage $documentationPage
     * @return \Illuminate\Http\Response
     *
     */
    public function update(DocumentationPageUpdateRequest $request, DocumentationPage $documentationPage)
    {
        $this->documentationPageRepository->update($documentationPage->id, $request->all());

        return redirect()
            ->action('DocumentationPagesController@edit', $documentationPage)
            ->with('success', 'Documentation Page updated');
    }

    /**
     * Delete a DocumentationPage resource
     *
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $this->documentationPageRepository->delete($id);

        return redirect()
            ->route('documentation.pages.index')
            ->with('info', 'Documentation page deleted.');
    }
}
