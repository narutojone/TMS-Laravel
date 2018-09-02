<?php

namespace App\Repositories\Client;

use App\Http\Requests\Request;

/**
 * Validation rules that are used when we try to create an Client
 */
class ClientCreateRequest extends Request
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
            'manager_id'          => 'required|numeric|exists:users,id',
            'employee_id'         => 'required|numeric|exists:users,id',
            'name'                => 'required|max:255',
            'organization_number' => 'required_if:internal,0|nullable|numeric|digits:9|unique:clients',
            'zendesk_id'          => 'sometimes|nullable|numeric',
            'system_id'           => 'required|numeric|exists:systems,id',
            'type'                => 'sometimes|numeric|in:'.Client::TYPE_AS.','.Client::TYPE_ENK,
            'paid'                => 'sometimes|boolean',
            'active'              => 'sometimes|boolean',
            'paused'              => 'sometimes|boolean',
            'internal'            => 'sometimes|boolean',
            'show_folders'        => 'sometimes|boolean',
            'risk'                => 'sometimes|boolean',
            'risk_reason'         => 'sometimes|nullable',
            'complaint_case'      => 'sometimes|boolean',
            'contact_type'        => 'required|string|in:new,existing',
            'contact_id'          => 'required_if:contact_type,existing|nullable',
            'contact_name'        => 'required_if:contact_type,new|nullable|max:255|unique:contacts,name',
            'contact_email'       => 'required_if:contact_type,new|nullable|email|unique:contact_emails,address',
            'contact_phone'       => 'required_if:contact_type,new|nullable|numeric|unique:contact_phones,number|digits_between:10,13',
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
