<?php

namespace App\Http\Controllers;

use App\Http\Requests\Settings\PhoneSettings;
use App\Repositories\PhoneSystemLog\PhoneSystemLog;
use App\Repositories\SystemSettingGroup\SystemSettingGroup;
use App\Repositories\SystemSettingValue\SystemSettingValue;
use App\Repositories\SystemSettingValue\SystemSettingValueInterface;
use Illuminate\Support\Facades\Storage;

class PhoneSystemController extends Controller
{
    /**
     * PhoneSystemController constructor.
     */
    public function __construct()
    {
        $this->middleware('admin_only',  ['except' => ['callLogs']]);
        parent::__construct();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $group = SystemSettingGroup::phoneSystemSettings()->first();

        return view('phone.index', compact('group'));
    }

    /**
     * @param \App\Http\Requests\Settings\PhoneSettings $request
     *
     * @return mixed
     */
    public function update(PhoneSettings $request)
    {
        $systemSettingValuesRepository = app()->make(SystemSettingValueInterface::class);
        $nonFileSettings = $systemSettingValuesRepository->model()->where('input_type', '<>', SystemSettingValue::INPUT_TYPE_FILE)->get();

        if ($nonFileSettings) {
            foreach ($nonFileSettings as $nonFileSetting) {
                if ($request->has($nonFileSetting->setting_key)) {
                    $systemSettingValuesRepository->update($nonFileSetting->id, [
                        'value' => $request->get($nonFileSetting->setting_key)
                    ]);
                }
            }
        }

        foreach ($request->files as $key => $file) {
            // create path
            $filename = $key . '.' . $file->getClientOriginalExtension();

            // try to save file locally
            $put = Storage::disk('public')->putFileAs('phone_system', $request->file($key), $filename);

            if ($put) {
                // update setting value
                SystemSettingValue::whereSettingKey($key)->update(['value' => $filename]);
            }
        }

        return redirect()->route('settings.phone.index')->withMessage('Settings successfully updated.');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function callLogs()
    {
        $group = SystemSettingGroup::phoneSystemSettings()
            ->firstOrFail();

        $settings = $group->settings
            ->pluck('value', 'setting_key')
            ->all();

        $logs = PhoneSystemLog::with(['client', 'employee', 'task'])
            ->orderBy('id', 'DESC')
            ->paginate(30);

        return view('phone.call_logs', compact('logs', 'settings'));
    }
}
