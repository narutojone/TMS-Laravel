<?php

namespace App\Http\Controllers;

use App\Repositories\User\UserChangePasswordRequest;
use App\Repositories\User\UserInterface;

class ChangePasswordController extends Controller
{

    /**
     * @var $userRepository - EloquentRepositoryUser
     */
    private $userRepository;

    /**
     * ChangePasswordController constructor.
     *
     * @param UserInterface $userRepository
     */
    public function __construct(UserInterface $userRepository)
    {
        parent::__construct();

        $this->userRepository = $userRepository;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return void
     */
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserChangePasswordRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(UserChangePasswordRequest $request)
    {
        $user = $this->userRepository->changePassword($request->user(), $request->input('current_password'), $request->input('password'));

        return redirect()
            ->action('SettingsController@edit')
            ->with('success', 'Password changed.');
    }
}
