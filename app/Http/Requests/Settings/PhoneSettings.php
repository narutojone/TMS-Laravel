<?php

namespace App\Http\Requests\Settings;

use App\Repositories\SystemSettingGroup\SystemSettingGroup;
use Illuminate\Foundation\Http\FormRequest;

class PhoneSettings extends FormRequest
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
        $group = SystemSettingGroup::phoneSystemSettings()->firstOrFail();
        return $group->settings->pluck('validation_rule', 'setting_key')->all();
    }
}
