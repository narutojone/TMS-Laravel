<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Template\TemplateInterface;
use App\Repositories\Template\TemplateCreateRequest;
use App\Repositories\Template\TemplateTransformer;

class TemplateController extends Controller
{
    /**
     * @var $templateRepository - EloquentRepositoryTemplate
     */
    private $templateRepository;

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(TemplateInterface $templateRepository)
    {
        $this->middleware('admin_only');
        parent::__construct();

        $this->templateRepository = $templateRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $templates = $this->templateRepository->getFilteredTemplates($request);
        $path = $this->templateRepository->generatePagePathWithFilterParams($request);

        return $templates->paginate(25)->withPath($path);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Repositories\Template\TemplateCreateRequest  $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function store(TemplateCreateRequest $request)
    {
        $template = $this->templateRepository->create($request->all());
        
        return (new TemplateTransformer)->transform($template);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * 
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, int $id)
    {
        $template = $this->templateRepository->find($id);
        $template->load('notifications');

        return (new TemplateTransformer)->transform($template);
    }    

     /**
     * Update the specified resource in storage.
     *
     * @param  \App\Repositories\Template\TemplateCreateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TemplateUpdateRequest $request, int $id)
    {
        $template = $this->templateRepository->update($id, $request->all());
        
        return (new TemplateTransformer)->transform($template);
    }
}    