<?php

namespace App\Http\Controllers\Api;

use App\Repositories\User\User;
use App\Repositories\User\UserInterface;
use App\Repositories\User\UserCreateRequest;
use App\Repositories\User\UserTransformer;
use App\Repositories\User\UserUpdateRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;


class UserController extends Controller
{
    /**
     * @var $userRepository - EloquentRepositoryUser
     */
    private $userRepository;
    
    /**
    * UserController constructor.
    *
    * @param UserInterface $userRepository
    */
    public function __construct(UserInterface $userRepository)
    {
        parent::__construct();

        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->userRepository->make()->paginate(25);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Repositories\User\UserCreateRequest  $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function store(UserCreateRequest $request)
    {
        $user = $this->userRepository->create($request->all());

        return (new UserTransformer)->transform($user);
    }

     /**
     * Display the specified resource.
     *
     * @param  \App\Repositories\User\User  $user
     * 
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return (new UserTransformer)->transform($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Repositories\User\UserUpdateRequest  $request
     * @param  User  $user
     * 
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        $user = $this->userRepository->update($user->id, $request->all());

        return (new UserTransformer)->transform($user);    
    }

    /**
     * Activate user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Repositories\User\User  $user
     * @return \Illuminate\Http\Response
     */
    public function activate(Request $request, User $user)
    {
        $user = $this->userRepository->activate($user->id);

        return (new UserTransformer)->transform($user);    
    }

    /**
     * Deactive a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Repositories\User\User  $user
     * @return \Illuminate\Http\Response
     */
    public function deactivate(Request $request, User $user)
    {
        $user = $this->userRepository->deactivate($user, $request->all(), $request->user()->id);
        
        return (new UserTransformer)->transform($user);   
    }

    /**
     * Return the clients the user has access to.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Repositories\User\User  $user
     * 
     * @return \Illuminate\Http\Response
     */
    public function clients(Request $request, User $user)
    {
        // Error if admin
        if ($user->hasRole(User::ROLE_ADMIN) || $user->hasRole(User::ROLE_CUSTOMER_SERVICE)) {
            return ['error' => 'Administrators are not assigned clients.'];
        }

        $clients = $user->getAccessibleClientsQuery()->get();

        // Custom paginator because of union in the query.
        $perPage = 25;
        $page = $request->input('page', 1);

        $slice = array_slice($clients->toArray(), $perPage * ($page - 1), $perPage);

        return new LengthAwarePaginator($slice, count($clients), $perPage, $page, [
            'path' => url('/clients'),
        ]);
    }
}