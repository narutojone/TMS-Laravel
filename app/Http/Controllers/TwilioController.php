<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use \Services_Twilio_Twiml;
use Illuminate\Http\Request;
use App\Repositories\Task\Task;
use App\Repositories\User\User;
use App\Repositories\Client\Client;
use App\Repositories\Task\TaskInterface;
use App\Repositories\Template\TemplateInterface;
use App\Repositories\PhoneSystemLog\PhoneSystemLog;
use App\Repositories\ContactPhone\ContactPhoneInterface;
use App\Repositories\SystemSettingGroup\SystemSettingGroup;
use App\Repositories\PhoneSystemLog\PhoneSystemLogInterface;

use Illuminate\Support\Facades\Storage;


class TwilioController extends Controller
{
    /**
     * @var \App\Repositories\SystemSettingValue\SystemSettingValue[]|array|\Illuminate\Database\Eloquent\Collection|mixed
     */
    private $settings = [];

    /**
     * TwilioController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->settings = $this->getSettings();
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Services_Twilio_Twiml
     */
    public function incomingCall(Request $request)
    {
        // get From phone number from Twilio Request Body
        $phone = $request->get('From');

        if ($phone) {
            // clear phone input from all non numeric symbols
            $phone = preg_replace("/[^0-9]/", "", $phone);

            // find client by phone number with employee relation
            $contactPhoneRepository = app()->make(ContactPhoneInterface::class);
            $contactPhone = $contactPhoneRepository->model()->where('number', $phone)->first();

            // Check if we find contact phone
            if ($contactPhone){
            	$client = $contactPhone->contact->clients->first();

            	if ($client && $client->paid) {
	                // check if employee has a phone number
	                if ($client->employee && $client->employee->phone) {
	                    return $this->makeTwilioResponse($client);
	                }
            	}
            }
        }

        return $this->dialFallBackNumber();
    }

    /**
     * @param \App\Repositories\Client\Client $client
     *
     * @return \Services_Twilio_Twiml
     */
    protected function makeTwilioResponse(Client $client)
    {
        $response = new Services_Twilio_Twiml();
        $fileToPlay = env('APP_TWILIO_HANDLER_URL') . Storage::url('phone_system/' . $this->settings['greeting-message']);
        $response->play($fileToPlay);
        $response->gather([
            'input' => 'dtmf',
            'action' => env('APP_TWILIO_HANDLER_URL') . 'twilio/process-gather/' . $client->id . '/' . $client->employee_id,
            'timeout' => env('APP_TWILIO_TIMEOUT'),
            'numDigits' => env('APP_TWILIO_NUM_DIGITS')
        ]);

        return $response;
    }

    /**
     * @param Request $request
     * @param $client_id
     * @param $employee_id
     * @return Services_Twilio_Twiml
     */
    public function processGather(Request $request, $client_id, $employee_id)
    {
        $digits = $request->get('Digits');
        if ($digits == 1) {
            $client = Client::has('employee')->find($client_id);
            if ($client) {
                if ($client->employee->phone) {
                    return $this->dialPhoneNumber($client->employee->format_phone_number, true, false, 10);
                }
            }
        }

        return $this->dialFallBackNumber();
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param $number
     * @param boolean $fallBackNumber
     *
     * @return \Services_Twilio_Twiml
     */
    public function record(Request $request, $number, $fallBackNumber = false)
    {
        $mediaDuration = null;
        $from = (int)$request->get('From');
        $DialCallStatus = $request->get('DialCallStatus');
        $attributes = [
            'call_id' => $request->get('CallSid'),
            'from' => $from,
            'to' => $number,
        ];

        $contactPhoneRepository = app()->make(ContactPhoneInterface::class);
        $contactPhone = $contactPhoneRepository->model()->where('number', $from)->first();

        // Check if we find contact phone
        if ($contactPhone) {
            $client = $contactPhone->contact->clients->first();

            if ($client) {
                $attributes['client_id'] = $client->id;

                $employee = User::wherePhone($number)->first();
                if ($employee) {
                    $attributes['employee_id'] = $employee->id;
                }

                if ($DialCallStatus == PhoneSystemLog::STATUS_COMPLETED) {
                    $attributes['media_duration'] = $request->get('RecordingDuration');
                    $mediaDuration = $request->get('RecordingDuration');
                }

                $phoneSystemLogRepository = app()->make(PhoneSystemLogInterface::class);
                $createPhoneSystemLog = $phoneSystemLogRepository->create($attributes);

                if ($createPhoneSystemLog && in_array($DialCallStatus, [PhoneSystemLog::STATUS_BUSY, PhoneSystemLog::STATUS_NO_ANSWER])) {

                    $templateRepository = app()->make(TemplateInterface::class);
                    $taskRepository = app()->make(TaskInterface::class);

                    $template = $templateRepository->find(59);
                    $task = $taskRepository->create([
                        'user_id'       => $client->employee->id,
                        'client_id'     => $client->id,
                        'template_id'   => $template->id,
                        'repeating'     => Task::NOT_REPEATING,
                        'deadline'      => Carbon::now()->addDays(1)->format('Y-m-d H:i:s'),
                    ]);

                    $createPhoneSystemLog->update(['task_id' => $task->id]);
                }

                $this->sendSmsNotification($DialCallStatus, $number, $client->name, $from, $mediaDuration);
            }
        }

        $response = new Services_Twilio_Twiml();
        if ($fallBackNumber == false && $DialCallStatus == PhoneSystemLog::STATUS_NO_ANSWER) {
            $fileToPlay = env('APP_TWILIO_HANDLER_URL') . Storage::url('phone_system/' . $this->settings['no-response']);
            $response->play($fileToPlay);
            $response->handup();
        }
        return $response;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param $number
     *
     * @return \Services_Twilio_Twiml
     */
    public function recordCallback(Request $request, $number)
    {
        $sid = $request->get('CallSid');
        $twilio = new \Services_Twilio(env('TWILIO_SID'), env('TWILIO_TOKEN'));
        $call = $twilio->account->calls->get($sid);
        $recordingData = null;
        foreach ($twilio->account->recordings->getIterator(0, 50, ["CallSid" => $sid]) as $recording) {
            $recordingData = $recording;
            break;
        }

        $phoneSystemLogRepository = app()->make(PhoneSystemLogInterface::class);
        $phoneSystemLog = $phoneSystemLogRepository->model()->where('call_id', $sid)->first();
        if ($phoneSystemLog) {
            $phoneSystemLogRepository->update($phoneSystemLog->id, [
                'call_duration' => $call->duration,
                'media_file' => 'https://api.twilio.com' . $recordingData->uri,
                'start_time' => Carbon::parse($call->start_time)->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s'),
                'end_time' => Carbon::parse($call->end_time)->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s'),
            ]);
        }

        $response = new Services_Twilio_Twiml();
        return $response;
    }

    /**
     * @param $number
     * @param bool $hideCallerRealPhoneNumber
     * @param bool $fallBackNumber
     * @param bool|integer $timeout
     * @return Services_Twilio_Twiml
     */
    protected function dialPhoneNumber($number, $hideCallerRealPhoneNumber = true, $fallBackNumber = false, $timeout = false)
    {
        $response = new Services_Twilio_Twiml();
        $phoneNumber = preg_replace("/[^0-9]/", "", $number);

        $defaultDialAttributes = [
            'action' => env('APP_TWILIO_HANDLER_URL') . 'twilio/record/' . $phoneNumber . '/' . $fallBackNumber,
            'record' => 'record-from-answer',
            'recordingStatusCallback' => env('APP_TWILIO_HANDLER_URL') . 'twilio/record-callback/' . $phoneNumber,
        ];

        if ($hideCallerRealPhoneNumber) {
            $defaultDialAttributes['callerId'] = env('TWILIO_FROM');
        }

        if ($fallBackNumber == false && $timeout) {
            $defaultDialAttributes['timeout'] = $timeout;
        }

        $dial = $response->dial($defaultDialAttributes);
        $dial->number($number);

        return $response;
    }

    /**
     * @return \Services_Twilio_Twiml
     */
    protected function dialFallBackNumber()
    {
        return $this->dialPhoneNumber('+' . $this->settings['fallback-number'], false, true);
    }

    /**
     * Get array of all phone system settings
     *
     * @return mixed
     */
    protected function getSettings()
    {
        $group = SystemSettingGroup::phoneSystemSettings()->firstOrFail();

        return $group->settings->pluck('value', 'setting_key')->all();
    }

    /**
     * Send sms notification to user
     *
     * @param $dialCallStatus
     * @param $toPhoneNumber
     * @param $clientName
     * @param $clientPhone
     * @param null $callDuration
     */
    protected function sendSmsNotification($dialCallStatus, $toPhoneNumber, $clientName, $clientPhone, $callDuration = null)
    {
        $settingKey = null;
        if ($dialCallStatus == PhoneSystemLog::STATUS_COMPLETED) {
            $settingKey = 'completed_call';
        } else if (in_array($dialCallStatus, [PhoneSystemLog::STATUS_BUSY, PhoneSystemLog::STATUS_NO_ANSWER])) {
            $settingKey = 'missed_call';
        }

        if (array_key_exists($settingKey, $this->settings)) {

            $text = $this->settings[$settingKey];

            if (!starts_with('+', $toPhoneNumber)) {
                $toPhoneNumber = '+' . $toPhoneNumber;
            }

            if (!starts_with('+', $clientPhone)) {
                $clientPhone = '+' . $clientPhone;
            }

            // Show call duration in minutes (MM:SS)
            $callDuration = gmdate("i:s", $callDuration);

            $search = ['[[clientname]]', '[[clientphone]]', '[[call_duration]]'];
            $replace = [$clientName, $clientPhone, $callDuration];
            $message = str_replace($search, $replace, $text);

            notification('sms')
                ->message($message)
                ->to($toPhoneNumber)
                ->send();
        }
    }
}
