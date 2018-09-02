<?php

namespace App\Http\Requests;

use App\Repositories\Flag\Flag;
use App\Repositories\User\User;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreFlagUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'flag_id' => 'required',
        ];
    }

    /**
     * @param \App\Repositories\User\User $user
     */
    public function persist(User $user)
    {
		$newFlag = Flag::where('id', $this->flag_id)->first();
		if(is_null($newFlag->days)) {
			$newExpirationDate = null;
			$newFlagStatus = 1;
		}
		else {
			$newExpirationDate = Carbon::now()->addDays($newFlag->days)->toDateTimeString();
			$newFlagStatus = 0;
		}


        if ($user->hasFlags()) {
            $flag = $user->lastFlag();
			if( (is_null($newFlag->days)) || (!is_null($newFlag->days) && !is_null($flag->pivot->expirationDate) && strtotime($newExpirationDate) > strtotime($flag->pivot->expirationDate)) ) {
				$flag->pivot->active = 0;
				$flag->pivot->save();
				$newFlagStatus = 1;
			}
        }
		else {
			$newFlagStatus = 1;
		}

        $user->flags()->attach($this->flag_id, [
			'comment'			=> $this->get('comment'),
			'active'			=> $newFlagStatus,
			'expirationDate'	=> $newExpirationDate,
		]);
    }
}
