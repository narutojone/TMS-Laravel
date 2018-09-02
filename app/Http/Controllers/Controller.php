<?php

namespace App\Http\Controllers;
use Auth;
use App\Repositories\Review\ReviewInterface;
use Illuminate\Support\Facades\View;
use App\Repositories\ProcessedNotification\ProcessedNotification;
use App\Repositories\ProcessedNotification\ProcessedNotificationInterface;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $user;

    protected $response;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->middleware('before_request');

        if (method_exists($this, 'boot')) {
            app()->call([$this, 'boot']);
        }

        // Make available the logged user in construct and call methods if any.
        $this->middleware(
            /**
            * @param $request
            * @param $next
            * @return mixed
            */
            function ($request, $next) {
                $this->user= Auth::user();
                $this->checkForPendingReviews();
                return $next($request);
            });

        $this->checkForPendingNotificationApprovals();
    }

    /**
     * Check for pending reviews for a reviewer and make the available to all views.
     */
    protected function checkForPendingReviews()
    {
        $reviewsPendingCount = 0;
        if (isset($this->user) && !empty($this->user)) {
            $reviewRepository = app()->make(ReviewInterface::class);
            $reviewsPending = $reviewRepository->getReviewsPending($this->user->id);
            $reviewsPendingCount = count($reviewsPending->toArray());
        }

        View::share('reviewsPending', $reviewsPendingCount);
    }

    /**
     * Check for pending notification approvals
     */
    public function checkForPendingNotificationApprovals()
    {
        $processedNotificationRepository = app()->make(ProcessedNotificationInterface::class);
        $processedNotificationPending = $processedNotificationRepository->model()->where('status', ProcessedNotification::STATUS_PENDING)->get()->count();

        View::share('processedNotificationPending', $processedNotificationPending);
    }
}
