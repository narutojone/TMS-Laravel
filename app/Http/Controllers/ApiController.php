<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * Display the API token page.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return view('api');
    }

    /**
     * Regenerate the API token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function regenerate(Request $request)
    {
        $request->user()->api_token = str_random(60);
        $request->user()->save();

        return redirect()
            ->action('ApiController@show')
            ->with('success', 'Api token regenerated.');
    }
}
