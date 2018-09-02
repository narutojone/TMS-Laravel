<?php

namespace App\Jobs;

use Aloha\Twilio\Twilio;
use App\Events\SmsMessageWasSent;
use App\Services\TmsNotifier;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendTwilioMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var
     */
    protected $to;

    /**
     * @var
     */
    protected $message;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * Create a new job instance.
     *
     * @param $to
     * @param $message
     * @param $data
     */
    public function __construct($to, $message, $data)
    {
        $this->to = $to;
        $this->message = $message;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $twilio = new Twilio(
            config('twilio.twilio.connections.twilio.sid'),
            config('twilio.twilio.connections.twilio.token'),
            config('twilio.twilio.connections.twilio.smsfrom')
        );

        $this->assignNecessaryData();

        $response = $twilio->message(
            $this->to, $this->message, $this->data['media'], $this->data['params']
        );

        if (! is_null($response->error_code)) {
            app('log')->error('Twilio: error: [' . $response->error_code . '] ' . $response->error_message);
        }

        event(new SmsMessageWasSent($this->to, $this->message));
    }

    /**
     * @return void
     */
    protected function assignNecessaryData() : void
    {
        $keys = ['media', 'params'];

        foreach ($keys as $key) {
            if (! isset($this->data[$key])) {
                $this->data[$key] = [];
            }
        }
    }
}
