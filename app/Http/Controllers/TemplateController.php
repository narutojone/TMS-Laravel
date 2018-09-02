<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Template\TemplateInterface;
use App\Repositories\Template\TemplateCreateRequest;
use App\Repositories\Template\TemplateUpdateRequest;

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
        $this->middleware('admin_only')->except('users');
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
        
        // Get the available categories    
        $categories = $this->templateRepository->make()
            ->groupBy('category')
            ->select('category')
            ->get()
            ->pluck('category');
        
        // Return the view with data for the web interface
        return view('templates.index')->with([
            'categories'       => $categories,
            'currentSearch'    => $request->input('search'),
            'selectedCategory' => $request->input('category'),
            'templates'        => $templates->orderBy('title')->paginate(25)->withPath($path),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = $this->templateRepository->getExistingCategories();

        return view('templates.create')->with([
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Repositories\Template\TemplateCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TemplateCreateRequest $request)
    {
        $template = $this->templateRepository->create($request->all());

        return redirect()
            ->action('TemplateController@show', $template)
            ->with('success', 'Template created.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id - id of the resource Template
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, int $id)
    {
        $template = $this->templateRepository->find($id);
        $template->load('notifications', 'overdueReasons');

        $subtasks = $template->subtasks()->where('active', 1)->orderBy('order', 'ASC')->get();

        return view('templates.show')->with([
            'template' => $template,
            'subtasks' => $subtasks,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id - id of the resource Template
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        $template = $this->templateRepository->find($id);
        $categories = $this->templateRepository->getExistingCategories();
            
        return view('templates.edit')->with([
            'categories' => $categories,
            'template'   => $template,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Repositories\Template\TemplateUpdateRequest  $request
     * @param  int  $id - id of the resource Template
     * @return \Illuminate\Http\Response
     */
    public function update(TemplateUpdateRequest $request, int $id)
    {
        $template = $this->templateRepository->update($id, $request->all());
        
        return redirect()
            ->action('TemplateController@show', $template)
            ->with('success', 'Template saved.');
    }

    /**
     * Duplicate the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id - id of the resource Template
     * @return \Illuminate\Http\Response
     */
    public function duplicate(Request $request, int $id)
    {
        $newTemplate = $this->templateRepository->duplicate($id);

        // Return with success message on new duplicated template
        return redirect()
            ->action('TemplateController@show', $newTemplate)
            ->with('success', 'Template duplicated.');

    }

    /**
     * Deactivate template
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id - id of the resource Template
     * @return \Illuminate\Http\Response
     */
    public function deactivate(Request $request, int $id)
    {
        $this->templateRepository->update($id, [
            'active' => 0,        
        ]);

        return redirect()
            ->action('TemplateController@index')
            ->with('info', 'Template deactivated.');
    }

    /**
     * @param int $id - id of the resource Template
     *
     * @return mixed
     */
    public function users(int $id)
    {
        $template = $this->templateRepository->find($id);
        $template->load('groups.users');
    
        $users = $template->groups->pluck('users')->flatten()->filter(function ($user) {
            return $user->active == 1;
        })->values();

        return $users;
    }
}
