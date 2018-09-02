<?php

use \App\Repositories\SystemSettingValue\SystemSettingValueInterface;
use Illuminate\Database\Migrations\Migration;

class AddNoResponsePhoneSystemSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $systemSettingValuesRepository = app()->make(SystemSettingValueInterface::class);

        $systemSettingValuesRepository->create([
            'group_id' => 1,
            'setting_key' =>  'no-response',
            'input_type' => 'file',
            'name' => 'No response',
            'validation_rule' => 'file|mimetypes:audio/mpeg',
        ]);
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
