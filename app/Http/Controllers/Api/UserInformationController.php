<?php

namespace App\Http\Controllers\Api;

use App\Filters\InformationFilters;
use App\Http\Controllers\Controller;
use App\Repositories\Information\InformationTransformer;
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
        $informations = auth()
            ->user()
            ->information()
            ->filter($informationFilters)
            ->paginate(25);

        return $informations;
    }

    /**
     * @param Request $request
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

        return (new InformationTransformer)->transform($information);
    }
}
