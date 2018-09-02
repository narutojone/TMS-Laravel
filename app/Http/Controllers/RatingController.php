<?php

namespace App\Http\Controllers;

use App\Repositories\Rating\Rating;
use App\Repositories\RatingRequest\RatingRequest;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $ratings = Rating::orderBy('id', 'DESC');
        $average = Rating::selectRaw('(sum(rate)) / (count(*)) as a')->groupBy('commentable_type')->get();

        if ( empty($average->toArray()) ) {
            $average = null;
        }

        // Order results if this is not an API request
        if (!$request->wantsJson()) {
            if ($request->rate) {
                $ratings->where('rate', 'like', $request->input('rate', '%'));
            }

            if ($request->type) {
                $type = $request->type == 'client' ? 'App\Repositories\Client\Client' : 'App\Repositories\User\User';
                $ratings->where('commentable_type', $type);
            }
        }

        return view('reports.rating.index')->with([
            'orderOptions' => [
                1=>'1 Star',
                2=>'2 Stars',
                3=>'3 Stars',
                4=>'4 Stars',
                5=>'5 Stars',
            ],
            'ratings' => $ratings,
            'average' => $average ? number_format($average->first()->a, 2) : 0,
        ]);
    }
    /**
     * @param string $hash
     * @param $rate
     *
     * @return \Illuminate\Http\RedirectResponse|string
     */
    public function store($hash, $rate)
    {
        if (! $request = RatingRequest::where('token', $hash)->first()) {
            return view('ratings.invalidlink');
        }

        if (in_array($rate, [1, 2])) {
            return redirect()->route('ratings.feedback.create', ['hash' => $hash, 'rate' => $rate]);
        }

        $ratingable = decrypt($request->ratingable);
        $commentable = decrypt($request->commentable);

        $request->delete();

        Rating::create([
            'ratingable_id'     => $ratingable['id'],
            'ratingable_type'   => $ratingable['type'],
            'commentable_id'    => $commentable['id'],
            'commentable_type'  => $commentable['type'],
            'rate'              => $rate,
            'reviewed'          => 0,
        ]);

        return view('ratings.thankyou');
    }

    /**
     * @param Rating $rating
     * @return \Illuminate\Http\RedirectResponse
     */
    public function review(Rating $rating)
    {
        $rating->reviewed = 1;
        $rating->save();

        return back()
            ->with('success', "Rating for {$rating->ratingable->name} has been reviewed.");
    }
}
