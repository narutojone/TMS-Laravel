<?php

namespace App\Http\Controllers;

use App\Repositories\EmailTemplate\EmailTemplate;
use App\Repositories\EmailTemplate\EmailTemplateInterface;
use App\Repositories\TemplateFolder\TemplateFolder;
use App\Repositories\TemplateFolder\TemplateFolderInterface;
use App\Repositories\Template\Template;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{

    /**
     * @var $emailTemplateRepository - EloquentRepositoryEmailTemplate
     */
    private $emailTemplateRepository;

    /**
     * @var $templateFolderRepository - EloquentRepositoryTemplateFolder
     */
    private $templateFolderRepository;

    /**
     * @var string
     */
    protected $mailable = '\App\Mail\TemplateMailable';

     
    /**
     * EmailTemplateController constructor.
     *
     * @param EmailTemplateInterface $emailTemplateRepository
     * @param TemplateFolderInterface $templateFolderRepository
     */
    public function __construct(EmailTemplateInterface $emailTemplateRepository, TemplateFolderInterface $templateFolderRepository)
    {
        parent::__construct();

        $this->emailTemplateRepository = $emailTemplateRepository;
        $this->templateFolderRepository = $templateFolderRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Prepare index filters

        $templateFolders = $this->templateFolderRepository->getAll();

        return view('settings.email-templates.index', [
            'folderId' => 0,
            'templateFolders'    => $templateFolders
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail(Request $request)
    {
        // Prepare index filters
        $showDeactivated = $request->get('show_deactivated', false);
        $folder_id = $request->id;

        $emailTemplates = $this->emailTemplateRepository->model()
            ->where(function ($query) use ($showDeactivated) {
                if($showDeactivated == false) {
                    $query->where('active', 1);
                }
            })
            ->where('folder_id', $folder_id)
            ->orderBy('name', 'ASC')
            ->paginate(10);

        $folder_name = $this->templateFolderRepository->getTemplateFolderById($folder_id)->name;

        return view('settings.email-templates.view', [
            'folderId' => $folder_id,
            'folder_name' => $folder_name,
            'inactiveView' => false,
            'templates'    => $emailTemplates
        ]);
    }

    /**
     * @param \App\Repositories\EmailTemplate\EmailTemplate $emailTemplate
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(EmailTemplate $emailTemplate)
    {
        return view('settings.email-templates.show', compact('emailTemplate'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function add(Request $request)
    {
        return view('settings.email-templates.create', [
            'folderId' => $request->folderId,
        ]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required',
            'template_file' => 'required',
            'title'         => 'required',
            'content_html'  => 'required_with:content',
            'footer_html'   => 'required_with:footer',
            'active'        => 'required|boolean',
        ]);

        $template = EmailTemplate::create([
            'name'          => $request->input('name'),
            'template_file' => $request->input('template_file'),
            'title'         => $request->input('title'),
            'content'       => $request->input('content'),
            'content_html'  => html_entity_decode($request->input('content_html')),
            'footer'        => $request->input('footer'),
            'footer_html'   => html_entity_decode($request->input('footer_html')),
            'active'        => $request->input('active'),
            'folder_id'     => $request->input('folder_id'),
        ]);

        return redirect()
            ->route('email_templates.index')
            ->with('success', "Email template '{$template->name}' has been created successfully.");
    }

    /**
     * @param \App\Repositories\EmailTemplate\EmailTemplate $emailTemplate
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     */
    public function edit(EmailTemplate $emailTemplate)
    {
        return view('settings.email-templates.edit', compact('emailTemplate'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @param \App\Repositories\EmailTemplate\EmailTemplate $emailTemplate
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $request->validate([
            'name'          => 'required',
            'template_file' => 'required',
            'title'         => 'required',
            'content_html'  => 'required_with:content',
            'footer_html'   => 'required_with:footer',
            'active'        => 'required|boolean',
        ]);

        $emailTemplate->update([
            'name'          => $request->input('name'),
            'template_file' => $request->input('template_file'),
            'title'         => $request->input('title'),
            'content'       => $request->input('content'),
            'content_html'  => html_entity_decode($request->input('content_html')),
            'footer'        => $request->input('footer'),
            'footer_html'   => html_entity_decode($request->input('footer_html')),
            'active'        => $request->input('active'),
        ]);

        return redirect()
            ->route('email_templates.index')
            ->with('success', "Email template '{$emailTemplate->name}' has been updated successfully.");
    }

    /**
     * @param \App\Repositories\EmailTemplate\EmailTemplate $emailTemplate
     *
     * @return array
     */
    public function getTemplateVars(EmailTemplate $emailTemplate)
    {
        $content = $emailTemplate->content_html . $emailTemplate->footer_html;

        preg_match_all('/\[\[\w+\]\]/', $content, $matches);

        if (! count($matches)) {
            return response()->json([]);
        }

        $variables = collect($matches[0])->map(function ($var) {
            return str_replace(['[[', ']]'], ['', ''], $var);
        })->filter(function ($var) {
            return ! in_array($var, Template::$notificationDynamicVariables);
        })->values()->toArray();

        return response()->json($variables);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function send(Request $request) {
        $user = \Auth::user();
        
        $emailTemplate = $this->emailTemplateRepository->model()
            ->where('id', $request->id)
            ->first();

        try {
            Mail::to($user->email)->send(
                (new $this->mailable($emailTemplate, 'Email Test', [], 'Accounting Group AS'))->onQueue('emails')
            );
        } catch (\Exception $e) {
            app('log')->error($e->getMessage());
        }

        return redirect()->back();
    }

    /**
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function search(Request $request) {
        $showDeactivated = $request->get('show_deactivated', false);
        $filter_name = $request->get('template_name');
        $folder_id = $request->get('folder_id');

        $emailTemplates = $this->emailTemplateRepository->search($filter_name, $showDeactivated, $folder_id);

        if ($folder_id == 0) {
            $folder_name = 'All Templates';
        } else {
            $folder_name = $this->templateFolderRepository->getTemplateFolderById($folder_id)->name;
        }

        return view('settings.email-templates.view', [
            'folderId' => $folder_id,
            'folder_name' => $folder_name,
            'inactiveView' => false,
            'templates'    => $emailTemplates
        ]);
    }

    /**
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showall() {

        $emailTemplates = $this->emailTemplateRepository->getAll();

        return view('settings.email-templates.view', [
            'folder_name' => 'All Templates',
            'folderId' => 0,
            'inactiveView' => false,
            'templates'    => $emailTemplates
        ]);
    }

    /**
     * @param Request $request
     * 
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function createFolder(Request $request) {
        $folder_name = $request->get('folder_name');

        $this->templateFolderRepository->create([
            'name' => $folder_name
        ]);

        return redirect()->back();
    }
}
