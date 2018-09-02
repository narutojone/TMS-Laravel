<?php

namespace App\Http\Controllers;

use App\Repositories\Information\Information;
use Illuminate\Http\Request;

class ListInformationController extends Controller
{
    /**
     * ListInformationController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $information = Information::getForUser($user)->paginate(25);

        if ($request->wantsJson()) {
            return $information;
        }

        return view('information.list', compact('information', 'user'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function zendesk(Request $request)
    {
        return view('information.zendesk');
    }
    
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function harvest(Request $request)
    {
        return view('information.harvest');
    }
}
