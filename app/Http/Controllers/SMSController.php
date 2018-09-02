<?php

namespace App\Http\Controllers;

use App\Repositories\NotifierLog\NotifierLog;
use Illuminate\Http\Request;

class SMSController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('settings.sms.index', [
            'logs' => NotifierLog::with('client', 'user')->where('type', 'sms')->latest()->paginate(25)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('settings.sms.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'phone' => 'required|max:10|min:10',
            'message' => 'required|max:150',
        ]);

        // Send SMS
        $result = notification('sms')
            ->message($request->input('message'))
            ->to($request->input('phone'))
            ->saveSimpleSmsForApproving();

        // If the message was sent
        if ($result){
            return redirect()
                ->action('SMSController@create')
                ->with('success', 'SMS Sent.');
        }

        // If the message was not sent
        return redirect()
            ->action('SMSController@create')
            ->with('warning', 'SMS was not sent.');
    }
}
