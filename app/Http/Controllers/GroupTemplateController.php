<?php

namespace App\Http\Controllers;

use App\Repositories\Group\GroupInterface;
use App\Repositories\GroupTemplate\GroupTemplateCreateRequest;
use App\Repositories\GroupTemplate\GroupTemplateInterface;
use App\Repositories\Template\TemplateInterface;

class GroupTemplateController extends Controller
{

    /**
     * @var $templateRepository - EloquentRepositoryTemplate
     */
    private $templateRepository;

    /**
     * @var $groupTemplateRepository - EloquentRepositoryGroupTemplate
     */
    private $groupTemplateRepository;

    /**
     * @var $groupRepository - EloquentRepositoryGroup
     */
    private $groupRepository;

    /**
     * GroupTemplateController constructor.
     *
     * @param TemplateInterface $templateRepository
     */
    public function __construct(TemplateInterface $templateRepository, GroupTemplateInterface $groupTemplateRepository, GroupInterface $groupRepository)
    {
        parent::__construct();

        $this->templateRepository = $templateRepository;
        $this->groupTemplateRepository = $groupTemplateRepository;
        $this->groupRepository = $groupRepository;
    }

    /**
     * @param int $templateId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(int $templateId)
    {
        $template = $this->templateRepository->find($templateId);

        return view('groups.assign')->with([
            'template' => $template,
        ]);
    }

    /**
     * @param GroupTemplateCreateRequest $request
     * @param int $templateId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(GroupTemplateCreateRequest $request, int $templateId)
    {
        $template = $this->templateRepository->find($templateId);
        $groupTemplates = $this->groupTemplateRepository->batchCreate($request->input('groups'), $templateId);

        return redirect()
            ->action('TemplateController@show', $template)
            ->with('success', 'New groups have been assigned.');
    }

    /**
     * @param int $templateId
     * @param int $groupId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $templateId, int $groupId)
    {
        $template = $this->templateRepository->find($templateId);
        $group = $this->groupRepository->find($groupId);

        $result = $this->groupTemplateRepository->deleteTemplateGroup($template, $group);

        if ($result === false) {
            return back()
                ->with('error', "Group named \"{$group->name}\" hasn't been removed.");
        }

        return redirect()
            ->action('TemplateController@show', $template)
            ->with('success', "Group named \"{$group->name}\" has been removed successfully.");
    }
}
