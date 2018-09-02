<?php

namespace App\Console\Commands;

use App\Repositories\Client\Client;
use App\Repositories\Rating\Rating;
use App\Repositories\RatingRequest\RatingRequest;
use App\Repositories\RatingTemplate\RatingTemplate;
use App\Repositories\User\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendRatingRequestToClientsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rating:clients_requests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send rating requests to all clients based on rating template conditions.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (! $template = RatingTemplate::first()) {
            return $this->error("Rating template doesn't exists. You can create one in application settings.");
        }

        if (! $template->emailTemplate) {
            return $this->error("Email template for rating doesn't exists. You can create one in application settings.");
        }

        $clientsIdsNotEligible = Rating::where('commentable_type', Client::class)->get()->filter(function ($rating) use ($template) {
            return Carbon::now()->lt($rating->created_at->addDays($template->days_from_last_review));
        })->pluck('commentable_id')->toArray();

        $pendingRequests = RatingRequest::get()->map(function ($pendingRequest) {
            return decrypt($pendingRequest->commentable);
        })->pluck('id')->toArray();

        $clients = Client::selectRaw('clients.id, clients.name, clients.active, clients.internal, clients.employee_id, count(clients.id) as tasks_counted')
            ->join('tasks', 'clients.id', '=', 'tasks.client_id')
            ->has('employee')
            ->with('employee')
            ->whereNotNull('tasks.completed_at')
            ->whereNotIn('clients.id', array_merge($clientsIdsNotEligible, $pendingRequests))
            ->where('clients.internal', 0)
            ->where('clients.active', 1)
            ->groupBy('clients.id')
            ->having('tasks_counted', '>=', $template->tasks_completed)
            ->get();

        if (! count($clients)) {
            return $this->warn("We haven't found any clients matched template criteria.");
        }

        foreach ($clients as $client) {
            $clientEmail = $client->email();
            if(is_null($clientEmail)) {
                continue;
            }

            $request = RatingRequest::create([
                'token'       => str_random(32),
                'ratingable'  => encrypt(['type' => User::class, 'id' => $client->employee_id]),
                'commentable' => encrypt(['type' => Client::class, 'id' => $client->id]),
            ]);

            if (! $request) {
                $this->warn("Unable to create rating request for client {$client->name} (id: {$client->id})");
                continue;
            }

            notification('template')
                ->template($template->emailTemplate->id)
                ->subject('Gi oss en tilbakemelding')
                ->data([
                    'client_name' => $client->name,
                    'user_name'   => $client->employee->name,
                    'user_email'  => $client->employee->email,
                ])
                ->viewData(['hash' => $request->token])
                ->to($clientEmail)
                ->from(env('MAIL_FROM_ADDRESS'))
                ->send();

            $this->info("Rating request has been sent to client {$client->name} (id: {$client->id})");
        }
    }
}
