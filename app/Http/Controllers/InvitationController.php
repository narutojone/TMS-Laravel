<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Invitation\Invitation;
use App\Repositories\User\User;
use Auth;

class InvitationController extends Controller
{
    public function register(Invitation $invitation)
    {
        return view('auth.invitation')->withInvitation($invitation);
    }

    public function submit(Request $request, Invitation $invitation)
    {
        // Validate the form input
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        // Create the new user
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
			'role' => $invitation->role,
        ]);

        // Set the user to admin if it was an admin invitation
        $user->admin = $invitation->admin;
        $user->save();

        // Delete the invitation
        $invitation->delete();

        // Login the user and redirect to the dashboard
        Auth::login($user);

        return redirect('/dashboard');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Invitation::paginate(25);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Repositories\Invitation\Invitation  $invitation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Invitation $invitation)
    {
        $invitation->delete();

        if (!$request->wantsJson()) {
            return redirect()
                ->action('UserController@index')
                ->with('info', 'Invitation deleted.');
        }
    }
}
