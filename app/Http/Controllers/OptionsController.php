<?php

namespace App\Http\Controllers;

use App\Repositories\EmailTemplate\EmailTemplateInterface;
use App\Repositories\Group\GroupInterface;
use App\Repositories\OverdueReason\OverdueReasonInterface;
use App\Repositories\OooReason\OooReasonCreateRequest;
use App\Repositories\OooReason\OooReasonInterface;
use App\Repositories\OooReason\OooReasonUpdateRequest;
use App\Repositories\Option\Option;
use App\Repositories\Option\OptionInterface;
use App\Repositories\Option\OptionUpdateRequest;
use Illuminate\Http\Request;
use App\Repositories\Template\TemplateInterface;

class OptionsController extends Controller
{
    /**
     * @var $optionRepository - EloquentRepositoryOption
     */
    private $optionRepository;

    /**
     * @var $emailTemplateRepository - EloquentRepositoryEmailTemplate
     */
    private $emailTemplateRepository;

    /**
     * @var $overdueReasonsRepository - EloquentRepositoryOverdueReason
     */
    private $overdueReasonsRepository;

    /**
     * @var $templateRepository - EloquentRepositoryTemplate
     */
    private $templateRepository;
  
    /**
     * @var $groupRepository - EloquentRepositoryGroup
     */
    private $groupRepository;

    /**
     * OptionsController constructor.
     *
     * @param OptionInterface $optionRepository
     * @param EmailTemplateInterface $emailTemplateRepository
     * @param OverdueReasonInterface $overdueReasonsRepository
     * @param GroupInterface $groupRepository
     */
    public function __construct(OptionInterface $optionRepository, EmailTemplateInterface $emailTemplateRepository, OverdueReasonInterface $overdueReasonsRepository, TemplateInterface $templateRepository, GroupInterface $groupRepository)
    {
        parent::__construct();

        $this->optionRepository = $optionRepository;
        $this->emailTemplateRepository = $emailTemplateRepository;
        $this->overdueReasonsRepository = $overdueReasonsRepository;
        $this->templateRepository = $templateRepository;
        $this->groupRepository = $groupRepository;
    }

    /**
     * Show a list of all system options
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        // Authorize request
        $this->authorize('index', Option::class);

        $options = $this->optionRepository->make()->orderBy('order', 'ASC')->orderBy('id', 'DESC')->get();
        $emailTemplates = $this->emailTemplateRepository->make()->orderBy('name', 'ASC')->get();
        $overdueReasons = $this->overdueReasonsRepository->make()->orderBy('id', 'ASC')->get();
        $taskTemplates = $this->templateRepository->make()->orderBy('title', 'ASC')->get();
        $groups = $this->groupRepository->make()->orderBy('id', 'ASC')->get();

        $specialFields = [
            'client_activate_email_template'                  => 'emailTemplates',
            'client_deactivate_email_template'                => 'emailTemplates',
            'client_paid_email_template'                      => 'emailTemplates',
            'client_not_paid_email_template'                  => 'emailTemplates',
            'client_not_paid_weekly_automatic_email_template' => 'emailTemplates',
            'client_new_employee'                             => 'emailTemplates',
            'client_new_manager'                              => 'emailTemplates',
            'client_new_manager_and_employee'                 => 'emailTemplates',
            'client_paused_email_template'                    => 'emailTemplates',
            'client_not_paused_email_template'                => 'emailTemplates',
            'contract_created_email_template'                 => 'emailTemplates',
            'contract_updated_email_template'                 => 'emailTemplates',
            'overdue_reason_client_move'                      => 'overdueReasons',
            'overdue_reason_backdated_task'                   => 'overdueReasons',
            'overdue_reason_client_unpaused'                  => 'overdueReasons',
            'overdue_reason_client_paid'                      => 'overdueReasons',
            'group_yearly_statements_field'                   => 'groups',
            'client_paused_template'                          => 'taskTemplates',
            'client_paused_user_group'                        => 'groups',
            'client_not_paid_template'                        => 'taskTemplates',
            'client_not_paid_user_group'                      => 'groups',
            'client_high_risk_template'                       => 'taskTemplates',
            'client_high_risk_user_group'                     => 'groups',
            'user_rating_task_template_bad_rating'            => 'taskTemplates',
            'user_rating_task_group'                          => 'groups',
            'client_rating_task_template_bad_rating'          => 'taskTemplates',
            'client_rating_task_group'                        => 'groups',
            'client_rating_task_template'                     => 'taskTemplates',
        ];

        return view('settings.options.index')->with([
            'options'        => $options,
            'emailTemplates' => $emailTemplates,
            'taskTemplates'  => $taskTemplates,
            'overdueReasons' => $overdueReasons,
            'specialFields'  => $specialFields,
            'groups'         => $groups,
        ]);
    }

    /**
     * Update an option
     * 
     * @param Request $request
     * @param Option $option
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Option $option)
    {
        // Authorize request
        $this->authorize('update', Option::class);

        $this->optionRepository->update($option->id, $request->all());

        return redirect()->route('settings.options.index')->with('success', 'Option updated.');
    }
}
