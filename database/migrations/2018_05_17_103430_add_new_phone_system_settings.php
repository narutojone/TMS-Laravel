<?php

use Illuminate\Database\Migrations\Migration;
use \App\Repositories\SystemSettingGroup\SystemSettingGroup;
use \App\Repositories\SystemSettingGroup\SystemSettingGroupInterface;

class AddNewPhoneSystemSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $systemSettingsGroupRepository = app()->make(SystemSettingGroupInterface::class);
        $phoneSystemSettingsGroup = $systemSettingsGroupRepository->model()->where('group_key', SystemSettingGroup::PHONE_SYSTEM_SETTINGS_KEY)->first();
        if ($phoneSystemSettingsGroup) {

            $phoneSystemSettingsGroup->settings()->create([
                'setting_key' => 'completed_call',
                'input_type' => 'text',
                'name' => 'SMS message for completed call',
                'validation_rule' => 'required|string',
            ]);

            $phoneSystemSettingsGroup->settings()->create([
                'setting_key' => 'missed_call',
                'input_type' => 'text',
                'name' => 'SMS message for missed incoming call',
                'validation_rule' => 'required|string',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
