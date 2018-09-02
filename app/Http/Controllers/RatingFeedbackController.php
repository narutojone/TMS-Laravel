<?php

namespace App\Http\Controllers;

use App\Repositories\Rating\Rating;
use App\Repositories\Rating\RatingInterface;
use App\Repositories\RatingRequest\RatingRequest;
use Illuminate\Http\Request;

class RatingFeedbackController extends Controller
{
    /**
     * @param string $hash
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create($hash, $rate)
    {
        return view('ratings.feedback', compact('hash', 'rate'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param string $hash
     * @param $rate
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function store(Request $request, $hash, $rate)
    {
        $validated = $request->validate(['feedback' => 'required|min:3']);

        if (! $request = RatingRequest::where('token', $hash)->first()) {
            return view('ratings.invalidlink');
        }

        $ratingable = decrypt($request->ratingable);
        $commentable = decrypt($request->commentable);

        $request->delete();

        $ratingRepository = app()->make(RatingInterface::class);
        $ratingRepository->create([
            'ratingable_id' => $ratingable['id'],
            'ratingable_type' => $ratingable['type'],
            'commentable_id' => $commentable['id'],
            'commentable_type' => $commentable['type'],
            'feedback' => $validated['feedback'],
            'rate' => $rate,
        ]);

        return view('ratings.thankyou');
    }
}
