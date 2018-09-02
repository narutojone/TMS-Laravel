<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Rating\Rating;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Rating\RatingInterface;
use App\Repositories\Rating\RatingTransformer;

class RatingController extends Controller
{
    /**
     * Rating Repository
     *
     * @var \App\Repositories\Rating\RatingInterface
     */
    protected $ratingRepository;

    /**
     * Constructor
     *
     * @param \App\Repositories\Rating\RatingInterface $ratingRepository
     */
    public function __construct(RatingInterface $ratingRepository)
    {
        $this->ratingRepository = $ratingRepository;
    }

    /**
     * Get average rating.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        return (new RatingTransformer)->transform($this->ratingRepository->getAverageRating());
    }
}
