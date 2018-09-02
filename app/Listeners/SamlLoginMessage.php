<?php

namespace App\Listeners;

use App\Repositories\User\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SamlLoginMessage
{
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $messageId = $event->getSaml2Auth()->getLastMessageId();

        $existingLoginRecord = DB::table('saml_login_logs')->where('message_id', '=', $messageId)->first();
        if($existingLoginRecord) {
            abort(403, 'Unauthorized');
        }

        $userEmail = $event->getSaml2User()->getUserId();

        // Try to find TMS user based on email address
        $matchedUser = User::where('email','=', $userEmail)->where('active', 1)->first();

        // Log in user if it's matched and active
        if ($matchedUser) {
            Auth::login($matchedUser);
        }

        DB::table('saml_login_logs')->insert([
            'message_id' => $messageId,
            'email'      => $userEmail,
            'success'    => $matchedUser ? 1 : 0,
        ]);

        if(!$matchedUser) {
            abort(403, 'Unauthorized');
        }
    }
}
