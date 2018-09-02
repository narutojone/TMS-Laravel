<?php

namespace App\Http\Controllers;

use App\Repositories\Flag\Flag;
use App\Repositories\Flag\FlagCreateRequest;
use App\Repositories\Flag\FlagUpdateRequest;
use App\Repositories\OverdueReason\OverdueReasonInterface;
use App\Repositories\Template\Template;
use App\Repositories\TemplateOverdueReason\TemplateOverdueReason;
use App\Repositories\TemplateOverdueReason\TemplateOverdueReasonCreateRequest;
use App\Repositories\TemplateOverdueReason\TemplateOverdueReasonInterface;
use App\Repositories\TemplateOverdueReason\TemplateOverdueReasonUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TemplateOverdueReasonController extends Controller
{
    private $templateOverdueReasonRepository;
    private $overdueReasonRepository;

    /**
     * TemplateOverdueReasonController constructor.
     *
     * @param TemplateOverdueReasonInterface $templateOverdueReasonRepository
     * @param OverdueReasonInterface $overdueReasonRepository
     */
    public function __construct(
        TemplateOverdueReasonInterface $templateOverdueReasonRepository,
        OverdueReasonInterface $overdueReasonRepository
    ){
        parent::__construct();

        $this->templateOverdueReasonRepository = $templateOverdueReasonRepository;
        $this->overdueReasonRepository = $overdueReasonRepository;
    }

    /**
     * @param Template $template
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Template $template, Request $request)
    {
        $triggerTypes = [
            TemplateOverdueReason::TRIGGER_NONE        => 'None',
            TemplateOverdueReason::TRIGGER_CONSECUTIVE => 'Consecutive',
            TemplateOverdueReason::TRIGGER_TOTAL       => 'Total',
        ];

        $triggerActions = [
            TemplateOverdueReason::ACTION_HIDE_REASON       => 'Hide reason',
            TemplateOverdueReason::ACTION_PAUSE_CLIENT      => 'Pause client',
            TemplateOverdueReason::ACTION_DEACTIVATE_CLIENT => 'Deactivate client',
            TemplateOverdueReason::ACTION_REMOVE_EMPLOYEE   => 'Remove employee',
        ];

        $availableOverdueReasons = [];
        $overdueReasonNames = [];


        $overdueReasonsModels = $this->overdueReasonRepository->model()
            ->orderBy('reason', 'ASC')
            ->get();


        // Initially we have all overdue reasons with all types
        foreach ($overdueReasonsModels as $overdueReasonsModel) {
            $availableOverdueReasons[$overdueReasonsModel->id] = $triggerTypes;
            $overdueReasonNames[$overdueReasonsModel->id] = $overdueReasonsModel->reason;
        }

        // Remove reasons that already have type='none'
        $templateOverdueReasons = $this->templateOverdueReasonRepository->model()
            ->where('template_id', $template->id)
            ->where('trigger_type', TemplateOverdueReason::TRIGGER_NONE)
            ->get();
        foreach ($templateOverdueReasons as $templateOverdueReason) {
            if(array_key_exists($templateOverdueReason->overdue_reason_id, $availableOverdueReasons)) {
                unset($availableOverdueReasons[$templateOverdueReason->overdue_reason_id]);
            }
        }

        //
        $templateOverdueReasons = $this->templateOverdueReasonRepository->model()
            ->where('template_id', $template->id)
            ->whereIn('trigger_type', [TemplateOverdueReason::TRIGGER_CONSECUTIVE, TemplateOverdueReason::TRIGGER_TOTAL])
            ->get();
        foreach ($templateOverdueReasons as $templateOverdueReason) {
            if(array_key_exists($templateOverdueReason->overdue_reason_id, $availableOverdueReasons)) {
                unset($availableOverdueReasons[$templateOverdueReason->overdue_reason_id][TemplateOverdueReason::TRIGGER_NONE]);

                if ($templateOverdueReason->trigger_type == TemplateOverdueReason::TRIGGER_CONSECUTIVE) {
                    if(isset($availableOverdueReasons[$templateOverdueReason->overdue_reason_id][TemplateOverdueReason::TRIGGER_CONSECUTIVE])) {
                        unset($availableOverdueReasons[$templateOverdueReason->overdue_reason_id][TemplateOverdueReason::TRIGGER_CONSECUTIVE]);
                    }

                } elseif ($templateOverdueReason->trigger_type == TemplateOverdueReason::TRIGGER_TOTAL) {
                    if(isset($availableOverdueReasons[$templateOverdueReason->overdue_reason_id][TemplateOverdueReason::TRIGGER_TOTAL])) {
                        unset($availableOverdueReasons[$templateOverdueReason->overdue_reason_id][TemplateOverdueReason::TRIGGER_TOTAL]);
                    }
                }
            }

            if(empty($availableOverdueReasons[$templateOverdueReason->overdue_reason_id])) {
                unset($availableOverdueReasons[$templateOverdueReason->overdue_reason_id]);
            }
        }

        return view('templates.overdue-reasons.create')->with([
            'template'                => $template,
            'availableOverdueReasons' => $availableOverdueReasons,
            'triggerTypes'            => $triggerTypes,
            'triggerActions'          => $triggerActions,
            'overdueReasonNames'      => $overdueReasonNames,
        ]);
    }

    /**
     * @param FlagCreateRequest|TemplateOverdueReasonCreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(TemplateOverdueReasonCreateRequest $request)
    {
        $templateOverdueReason = $this->templateOverdueReasonRepository->create($request->all());

        return redirect()
            ->route('templates.show', $templateOverdueReason->template_id)
            ->with('success', 'Template overdue reason created');
    }

    /**
     * Show edit form for a TemplateOverdueReason
     *
     * @param Template $template
     * @param TemplateOverdueReason $templateOverdueReason
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @internal param Flag $flag
     *
     */
    public function show(Template $template, TemplateOverdueReason $templateOverdueReason)
    {
        $triggerActions = [
            TemplateOverdueReason::ACTION_HIDE_REASON       => 'Hide reason',
            TemplateOverdueReason::ACTION_PAUSE_CLIENT      => 'Pause client',
            TemplateOverdueReason::ACTION_DEACTIVATE_CLIENT => 'Deactivate client',
            TemplateOverdueReason::ACTION_REMOVE_EMPLOYEE   => 'Remove employee',
        ];

        return view('templates.overdue-reasons.show')->with([
            'template'                => $template,
            'templateOverdueReason'   => $templateOverdueReason,
            'triggerActions'          => $triggerActions,
        ]);
    }

    /**
     * Update a TemplateOverdueReason
     *
     * @param FlagUpdateRequest|TemplateOverdueReasonUpdateRequest $request
     * @param Template $template
     * @param TemplateOverdueReason $templateOverdueReason
     * @return Flag|\Illuminate\Http\RedirectResponse
     */
    public function update(TemplateOverdueReasonUpdateRequest $request, Template $template, TemplateOverdueReason $templateOverdueReason)
    {
        $this->templateOverdueReasonRepository->update($templateOverdueReason->id, $request->all());

        return redirect()
            ->route('templates.show', $template)
            ->with('success', 'Template overdue reason was updated successfully.');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param Template $template
     * @param TemplateOverdueReason $templateOverdueReason
     * @return \Illuminate\Http\RedirectResponse
     *
     */
    public function destroy(Request $request, Template $template, TemplateOverdueReason $templateOverdueReason)
    {
        $this->templateOverdueReasonRepository->delete($templateOverdueReason->id);

        return redirect()
            ->route('templates.show', $template)
            ->with('success', 'Overdue reason removed');
    }
}
