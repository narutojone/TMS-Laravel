<?php

namespace App\Repositories\SystemSettingValue;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\SystemSettingGroup\SystemSettingGroup;

class SystemSettingValue extends Model
{
    CONST INPUT_TYPE_FILE = 'file';

    protected $fillable = [
        'group_id',
        'name',
        'validation_rule',
        'input_type',
        'setting_key',
        'value'
    ];

    public function group()
    {
        return $this->belongsTo(SystemSettingGroup::class);
    }
}
