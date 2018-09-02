<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Repositories\Rating\Rating;
use App\Repositories\RatingRequest\RatingRequest;

class FixIssueWithRatingModels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Get all rating requests
        $rating_requests = RatingRequest::get();

        foreach($rating_requests as $rating_request){

            // Decrypt Ratingable & Commentable
            $ratingable = decrypt($rating_request->ratingable);
            $commentable = decrypt($rating_request->commentable);

            // Changing type for Ratingable & Commentable
            if($ratingable['type'] == 'App\User'){
                $ratingable['type'] = 'App\Repositories\User\User';
            }

            if($commentable['type'] == 'App\Client'){
                $commentable['type'] = 'App\Repositories\Client\Client';
            }

            // Encrypt Ratingable & Commentable and update/save
            $rating_request->ratingable = encrypt($ratingable);
            $rating_request->commentable = encrypt($commentable);
            $rating_request->save();

        }

        // Get all ratings
        $ratings = Rating::get();

        foreach($ratings as $rating){

            //Check for Ratingable & Commentable with wrong path
            if($rating->ratingable_type == 'App\User'){
                $rating->ratingable_type = 'App\Repositories\User\User';
            }

            if($rating->commentable_type == 'App\Client'){
                $rating->commentable_type = 'App\Repositories\Client\Client';
            }

            // Update and save
            $rating->save();

        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
