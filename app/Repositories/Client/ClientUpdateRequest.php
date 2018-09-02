<?php

namespace App\Repositories\Client;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to update an Client entity
 */
class ClientUpdateRequest extends Request
{

    protected $message = 'Request parameters for Client are not valid.';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'manager'             => 'sometimes|numeric|exists:users,id',
            'employee'            => 'sometimes|numeric|exists:users,id',
            'name'                => 'sometimes|max:255',
            'organization_number' => 'sometimes|nullable|numeric|digits:9',
            'system_id'           => 'sometimes|numeric|exists:systems,id',
            'paid'                => 'sometimes|boolean',
            'active'              => 'sometimes|boolean',
            'paused'              => 'sometimes|boolean',
            'internal'            => 'sometimes|boolean',
            'show_folders'        => 'sometimes|boolean',
            'harvest_id'          => 'sometimes|integer|nullable',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
