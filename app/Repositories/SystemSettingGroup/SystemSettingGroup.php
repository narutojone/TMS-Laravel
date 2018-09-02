<?php

namespace App\Repositories\SystemSettingGroup;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\SystemSettingValue\SystemSettingValue;

class SystemSettingGroup extends Model
{
    CONST PHONE_SYSTEM_SETTINGS_KEY = 'phone-system';

    protected $fillable = [
        'name',
        'group_key'
    ];

    public function settings()
    {
        return $this->hasMany(SystemSettingValue::class, 'group_id', 'id');
    }

    public function scopePhoneSystemSettings($query)
    {
        return $query->has('settings')->whereGroupKey(self::PHONE_SYSTEM_SETTINGS_KEY);
    }
}
