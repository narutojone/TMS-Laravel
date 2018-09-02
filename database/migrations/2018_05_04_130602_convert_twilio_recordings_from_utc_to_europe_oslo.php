<?php

use \Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use App\Repositories\PhoneSystemLog\PhoneSystemLogInterface;

class ConvertTwilioRecordingsFromUtcToEuropeOslo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $phoneSystemLogRepository = app()->make(PhoneSystemLogInterface::class);
        $phoneSystemLogs = $phoneSystemLogRepository->model()->whereNotNull('start_time')->whereNotNull('end_time')->get();
        if ($phoneSystemLogs) {
            foreach ($phoneSystemLogs as $phoneSystemLog) {

                $convertedAttributes = [
                    'start_time' => Carbon::parse($phoneSystemLog->start_time . ' UTC')->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s'),
                    'end_time' => Carbon::parse($phoneSystemLog->end_time . ' UTC')->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s'),
                ];

                $phoneSystemLogRepository->update($phoneSystemLog->id, $convertedAttributes);
            }
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
