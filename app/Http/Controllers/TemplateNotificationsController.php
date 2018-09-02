<?php

namespace App\Http\Controllers;

use App\Repositories\EmailTemplate\EmailTemplate;
use App\Repositories\EmailTemplate\EmailTemplateInterface;
use App\Repositories\ProcessedNotification\ProcessedNotificationInterface;
use App\Repositories\TemplateNotification\TemplateNotification;
use App\Repositories\TemplateNotification\TemplateNotificationInterface;
use App\Repositories\TemplateNotification\TemplateNotificationListRequest;
use Illuminate\Http\Request;
use App\Repositories\Template\Template;

class TemplateNotificationsController extends Controller
{

    private $templateNotificationRepository;
    private $processedNotificationRepository;
    private $emailTemplateRepository;

    /**
     * TemplateNotificationsController constructor.
     * @param TemplateNotificationInterface $templateNotificationRepository
     * @param ProcessedNotificationInterface $processedNotificationRepository
     * @param EmailTemplateInterface $emailTemplateRepository
     */
    public function __construct(
        TemplateNotificationInterface $templateNotificationRepository,
        ProcessedNotificationInterface $processedNotificationRepository,
        EmailTemplateInterface $emailTemplateRepository
    ){
        parent::__construct();

        $this->templateNotificationRepository = $templateNotificationRepository;
        $this->processedNotificationRepository = $processedNotificationRepository;
        $this->emailTemplateRepository = $emailTemplateRepository;
    }

    /**
     * @param \App\Repositories\Template\Template $template
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Template $template)
    {
        $emailTemplates = $this->emailTemplateRepository->model()->where('active', 1)->get();

        return view('templates.notifications.create')->with([
            'template'       => $template,
            'emailTemplates' => $emailTemplates,
        ]);
    }

    /**
     * @param  \Illuminate\Http\Request $request
     * @param \App\Repositories\Template\Template $template
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Template $template)
    {
        $data = $request->all();
        $data['vars'] = $request->filled('vars') ? $request->input('vars') : [];
        $data['template_id'] = $template->id;

        $this->templateNotificationRepository->create($data);

        return redirect()
            ->route('templates.show', $template)
            ->with('success', 'Notification has been created successfully.');
    }

    /**
     * @param \App\Repositories\Template\Template $template
     * @param \App\Repositories\TemplateNotification\TemplateNotification $notification
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function show(Template $template, TemplateNotification $notification)
    {
        $emailTemplate = null;
        if ($notification->type === 'template') {
            if ($emailTemplate = EmailTemplate::find($notification->details['template'])) {
                $vars = [];
                foreach ($notification->details['data'] as $name => $value) {
                    $vars["[[{$name}]]"] = $value;
                }

                $emailTemplate = view('layouts.emails.' . $emailTemplate->template_file, [
                    'title' => $emailTemplate->title,
                    'content' => str_replace(array_keys($vars), array_values($vars), $emailTemplate->content_html),
                    'footer' => str_replace(array_keys($vars), array_values($vars), $emailTemplate->footer_html),
                ])->render();
            }
        }

        if ($notification->type === 'email') {
            $emailTemplate = view('mail.default-mailable', [
                'content' => $notification->details['message'],
            ])->render();
        }

        $emailTemplates = $this->emailTemplateRepository->model()->where('active', 1)->get();

        return view('templates.notifications.show')->with([
            'template'       => $template,
            'notification'   => $notification,
            'emailTemplate'  => $emailTemplate,
            'emailTemplates' => $emailTemplates,
        ]);
    }

    /**
     * @param \App\Repositories\Template\Template $template
     * @param \App\Repositories\TemplateNotification\TemplateNotification $notification
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Template $template, TemplateNotification $notification)
    {
        $emailTemplates = $this->emailTemplateRepository->model()->where('active', 1)->get();

        return view('templates.notifications.edit')->with([
            'template'       => $template,
            'notification'   => $notification,
            'emailTemplates' => $emailTemplates,
        ]);
    }

    /**
     * @param  \Illuminate\Http\Request $request
     * @param \App\Repositories\Template\Template $template
     * @param \App\Repositories\TemplateNotification\TemplateNotification $notification
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Template $template, TemplateNotification $notification)
    {
        $data = $request->all();
        $data['vars'] = $request->filled('vars') ? $request->input('vars') : [];
        $data['template_id'] = $template->id;

        $this->templateNotificationRepository->update($notification->id, $data);

        return redirect()
            ->route('templates.show', $template)
            ->with('success', 'Notification has been updated successfully.');
    }

    /**
     * @param \App\Repositories\Template\Template $template
     * @param \App\Repositories\TemplateNotification\TemplateNotification $templateNotification
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Template $template, TemplateNotification $templateNotification)
    {
        $templateNotification->delete();

        return redirect()
            ->route('templates.show', $template)
            ->with('success', 'Notification has been deleted successfully.');
    }

    /**
     * @param \App\Repositories\TemplateNotification\TemplateNotification $notification
     * @return string
     */
    public function getTemplateVariables(TemplateNotification $notification)
    {
        return $notification['details']['data'] ?? '';
    }
}
