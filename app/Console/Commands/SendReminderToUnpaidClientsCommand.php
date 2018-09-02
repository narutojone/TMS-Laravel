<?php

namespace App\Console\Commands;

use App\Repositories\Client\Client;
use App\Repositories\ClientEditLog\ClientEditLog;
use App\Repositories\ClientEditLog\ClientEditLogInterface;
use App\Repositories\EmailTemplate\EmailTemplate;
use App\Repositories\Option\Option;
use App\Repositories\Option\OptionInterface;
use App\Repositories\Rating\Rating;
use App\Repositories\RatingRequest\RatingRequest;
use App\Repositories\RatingTemplate\RatingTemplate;
use App\Repositories\User\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendReminderToUnpaidClientsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:unpaid-clients';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders to not paid clients.';

    protected $optionRepository = null;

    protected $clientEditLogRepository = null;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        $this->optionRepository = app()->make(OptionInterface::class);
        $this->clientEditLogRepository = app()->make(ClientEditLogInterface::class);

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $sevenDaysAgo = Carbon::now()->subDays(7);
        $option = $this->optionRepository->model()->where('key', 'client_not_paid_weekly_automatic_email_template')->first();

        if (!$option->emailTemplate) {
            return $this->error("Email template doesn't exists. You can create one in application settings.");
        }

        // Fetch all clients and join logs table with applicable log field
        $clients = Client::select('clients.*', 'client_edit_logs.starts_at', 'client_edit_logs.id as client_edit_log_id', 'client_edit_logs.reminder_sent_at')
            ->join('client_edit_logs', function ($join) use ($sevenDaysAgo) {
                $join->on('clients.id', 'client_edit_logs.client_id')
                    ->where('client_edit_logs.field', 'paid')
                    ->where('client_edit_logs.value', '0')
                    ->where('client_edit_logs.starts_at', '<', $sevenDaysAgo)
                    ->whereNull('client_edit_logs.ends_at');
            })
            ->where('clients.active', Client::IS_ACTIVE)
            ->where('clients.paid', Client::NOT_PAID)
            ->get();

        foreach ($clients as $client) {
            $starts_at = Carbon::parse($client->starts_at)->startOfDay();
            // Check if client has been reminded today
            if ($client->reminder_sent_at && Carbon::today()->diffInDays($client->reminder_sent_at) < 1) {
                continue;
            }

            // Send on each 7th day after marking as not paid
            if ($sevenDaysAgo->startOfDay()->diffInDays($starts_at) % 7 === 0) {
                $clientEmail = $client->email();
                if (is_null($clientEmail)) {
                    continue;
                }

                $data = [
                    'clientname' => $client->name,
                    'employeename' => (isset($client->employee)) ? $client->employee->name : "",
                    'managername' => (isset($client->manager)) ? $client->manager->name : "",
                    'template_id'   => $option->emailTemplate->id,
                    'employeepf' => 'PF' . ((isset($client->employee)) ? $client->employee->pf_id : ""),
                ];

                notification('template')
                    ->template($option->emailTemplate->id)
                    ->subject($option->emailTemplate->title)
                    ->to($clientEmail)
                    ->from('')
                    ->data($data)
                    ->saveForApproving(null, $client->name, null);

                $this->clientEditLogRepository->update($client->client_edit_log_id, [
                    'reminder_sent_at' => Carbon::now()
                ]);

                $this->info("Reminder sent to (Client ID: " . $client->id . ")");
            }
        }
    }
}
