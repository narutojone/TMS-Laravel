<?php

namespace App\Http\Controllers;

use App\Repositories\ProcessedNotification\ProcessedNotification;
use App\Repositories\ProcessedNotification\ProcessedNotificationApproveDeclineRequest;
use App\Repositories\ProcessedNotification\ProcessedNotificationInterface;
use App\Repositories\ProcessedNotification\ProcessedNotificationListRequest;
use App\Repositories\ProcessedNotification\ProcessedNotificationTransformer;
use App\Repositories\TemplateNotification\TemplateNotification;
use App\Repositories\TemplateNotification\TemplateNotificationInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class ProcessedTemplatesController extends Controller
{
    /**
     * @property TemplateNotificationInterface templateNotificationRepository
     */
    protected $templateNotificationRepository;
    /**
     * @var ProcessedNotificationInterface
     */
    private $processedNotificationRepository;

    /**
     * TemplateNotificationsController constructor.
     * @param TemplateNotificationInterface $templateNotificationRepository
     * @param ProcessedNotificationInterface $processedNotificationRepository
     */
    public function __construct(TemplateNotificationInterface $templateNotificationRepository, ProcessedNotificationInterface $processedNotificationRepository)
    {
        parent::__construct();

        $this->templateNotificationRepository = $templateNotificationRepository;
        $this->processedNotificationRepository = $processedNotificationRepository;
    }

    /**
     * Show the list with processed notifications. (in our case, un approved) @todo: change comment if the scope of the function changes
     *
     * @param ProcessedNotificationListRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexPending(ProcessedNotificationListRequest $request)
    {
        $path = $this->processedNotificationRepository->generatePagePathWithFilterParams($request);

        $processedNotifications = $this->processedNotificationRepository->model()
            ->with(['templateNotification'])
            ->where('status', '=', ProcessedNotification::STATUS_PENDING)
            ->paginate(100)
            ->withPath($path);
        ;

        $processedNotifications->map(function ($processedNotification) {
            $processedNotification->decodedData = json_decode($processedNotification->data, true);

            return $processedNotification;
        });

        $request->session()->put("secondBackPage", $request->fullUrl());

        return view('templates.notifications.list-pending', [
            'processedNotifications'    => $processedNotifications,
        ]);
    }

    /**
     * List declined notifications
     *
     * @param ProcessedNotificationListRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexDeclined(ProcessedNotificationListRequest $request)
    {
        $path = $this->processedNotificationRepository->generatePagePathWithFilterParams($request);

        $processedNotifications = $this->processedNotificationRepository->model()
            ->with(['templateNotification'])
            ->where('status', '=', ProcessedNotification::STATUS_DECLINED)
            ->paginate(100)
            ->withPath($path);
        ;

        $processedNotifications = $this->processedNotificationRepository->getMappedNotifications($processedNotifications);


        $request->session()->put("secondBackPage", $request->fullUrl());

        return view('templates.notifications.list-declined', [
            'processedNotifications'    => $processedNotifications,
        ]);
    }

    /**
     * Mark a notification as approved.
     *
     * @param ProcessedNotificationApproveDeclineRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(ProcessedNotificationApproveDeclineRequest $request)
    {
        $processedNotificationIds = $request->input('processedNotificationIds', []);
        foreach ($processedNotificationIds as $processedNotificationId) {
            $data = [];
            $data['status'] = ProcessedNotification::STATUS_APPROVED;

            $processedNotification = $this->processedNotificationRepository->update($processedNotificationId, $data);
        }

        $originalRoute = $request->session()->get('secondBackPage', false);

        if ($originalRoute) {
            return redirect()
                ->to($originalRoute)
                ->with('success', 'Selected notifications were approved.');
        }

        return redirect()
            ->back()
            ->with('success', 'Selected notifications were approved.');
    }

    /**
     * Mark a notification as deleted, that means to delete it using soft deletes.
     *
     * @param ProcessedNotificationApproveDeclineRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function decline(ProcessedNotificationApproveDeclineRequest $request)
    {
        $processedNotificationIds = $request->input('processedNotificationIds', []);
        foreach ($processedNotificationIds as $processedNotificationId) {
            $data = [];
            $data['status'] = ProcessedNotification::STATUS_DECLINED;

            $processedNotification = $this->processedNotificationRepository->update($processedNotificationId, $data);
        }

        $originalRoute = $request->session()->get('secondBackPage', false);
        if ($originalRoute) {
            return redirect()
                ->to($originalRoute)
                ->with('success', 'Selected notifications were approved.');
        }

        return redirect()
            ->back()
            ->with('success', 'Selected notifications were declined.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $processedNotificationId
     * @return void
     */
    public function show(int $processedNotificationId)
    {
        $processedNotification = $this->processedNotificationRepository->find($processedNotificationId, ['templateNotification']);
        $processedNotificationTransformer = new ProcessedNotificationTransformer();
        $processedNotificationTransformed = $processedNotificationTransformer->transform($processedNotification);

        return view('processedtemplates.show')->with([
            'processedNotification' => $processedNotificationTransformed,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return void
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return void
     */
    public function destroy($id)
    {
        //
    }
}
