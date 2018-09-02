<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\NotifierLog\NotifierLog;

class NotifierLogController extends Controller
{
	/**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Repositories\NotifierLog\NotifierLog  $notification
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, NotifierLog $notification)
    {
        // Authorize request
        $this->authorize('view', $notification->client);

        // Replace links
        if($notification->type == 'email'){
            $body = str_replace('/delivered','#',json_decode($notification->body));        
        } else {
            $body = $notification->body;
        }

        // Show notification preview
        return view('notification.show')->with([
            'notification'  => $notification,
            'body'          => $body,
        ]);
    }
}
