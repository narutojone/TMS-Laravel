<?php

namespace App\Http\Controllers;

use App\Filters\InformationFilters;
use App\Repositories\InformationUser\InformationUserCreateRequest;
use App\Repositories\User\UserInterface;
use Illuminate\Http\Request;

class UserInformationController extends Controller
{
    /**
     * @var $userRepository - EloquentRepositoryInformationUser
     */
    private $informationUserRepository;

    /**
     * UserInformationController constructor.
     *
     * @param UserInterface $informationUserRepository
     */
    public function __construct(UserInterface $informationUserRepository)
    {
        parent::__construct();

        $this->informationUserRepository = $informationUserRepository;
    }

    /**
     * @param \App\Filters\InformationFilters $informationFilters
     *
     * @return mixed
     */
    public function index(InformationFilters $informationFilters)
    {
        $information = auth()
            ->user()
            ->information()
            ->filter($informationFilters)
            ->orderBy('id','DESC')
            ->paginate(25);

        return view('user-information.index')->with([
            'information' => $information,
        ]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param int $id - information id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, int $id)
    {
        $information = auth()
            ->user()
            ->information()
            ->where('information.id', $id)
            ->first();

        return view('user-information.show')->with([
            'information' => $information,
        ]);
    }

    /**
     * @param InformationUserCreateRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(InformationUserCreateRequest $request)
    {
        if (! $user = auth()->user()) {
            abort(404);
        }

        $user->information()->attach($request->information_id, ['accepted_status' => 1]);

        return redirect()->route('information.show', $request->information_id);
    }
}
