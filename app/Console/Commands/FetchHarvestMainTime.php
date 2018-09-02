<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use GuzzleHttp\Psr7\Response;
use Illuminate\Console\Command;
use App\Services\HarvestApiGateway;
use App\Repositories\User\UserInterface;
use App\Repositories\Client\ClientInterface;
use App\Repositories\HarvestMainTimeEntry\HarvestMainTimeEntryInterface;

class FetchHarvestMainTime extends Command
{
    CONST RATE_LIMIT_REQUESTS = 100;
    CONST RATE_LIMIT_SECONDS = 15;

    protected $signature = 'harvest:fetch-main-time {--mode=yesterday}';
    protected $description = 'Fetch time from Harvest and assign it to users';

    protected $harvestMainTimeEntryRepository;
    protected $clientRepository;
    protected $userRepository;

    protected $clientsMatchingArray;
    protected $usersMatchingArray;

    public function __construct(HarvestMainTimeEntryInterface $harvestMainTimeEntryRepository)
    {
        parent::__construct();
        $this->harvestMainTimeEntryRepository = $harvestMainTimeEntryRepository;
    }

    public function handle()
    {
        $mode = $this->option('mode');
        $list = $this->getAllTimeEntries($mode);

        if ($list) {
            foreach ($list as $item) {
                $find = $this->harvestMainTimeEntryRepository->model()->where('external_id', $item['external_id'])->first();
                if ($find) {
                    $this->harvestMainTimeEntryRepository->update($find['id'], [
                        'notes' => $item['notes'],
                        'hours' => $item['hours'],
                    ]);
                } else {
                    $this->harvestMainTimeEntryRepository->create($item);
                }
            }
        }
    }

    protected function getAllTimeEntries($mode)
    {
        $collection = [];
        $initialTime = time();
        $requests = 1;
        $path = 'https://api.harvestapp.com/v2/time_entries?';

        if ($mode == 'yesterday') {
            $previousDay = $this->getPreviousDay();
            $path .= http_build_query(['from' => $previousDay, 'to' => $previousDay]);
        } else if ($mode == 'today') {
            $today = Carbon::today()->format('Y-m-d');
            $path .= http_build_query(['from' => $today, 'to' => $today]);
        }

        $apiGateway = new HarvestApiGateway(true);
        $execute = $apiGateway->execute($path);
        /**
         * @var Response $response
         */
        $response = $execute->response;

        while (true) {
            $this->handleAPIResponse($response);

            $json = $execute->json();
            $collection = array_merge($collection, collect($json['time_entries'])->map(function ($item) {

                $hours = ($item['hours'] > 0) ? $item['hours'] : 0;

                return [
                    'external_id' => $item['id'],
                    'harvest_client_id' => $item['client']['id'],
                    'harvest_user_id' => $item['user']['id'],
                    'client_id' => $this->matchClient($item['project']['code'], $item['client']['id']),
                    'user_id' => $this->matchUser($item['user']['name'], $item['user']['id']),
                    'project_code' => $item['project']['code'],
                    'spent_date' => Carbon::parse($item['spent_date'])->format('Y-m-d'),
                    'hours' => $hours,
                    'notes' => $item['notes'],
                ];

            })->all());

            // Go to the next page if it exists
            if ($json['next_page']) {
                if ($requests == self::RATE_LIMIT_REQUESTS) {
                    $sleepDuration = self::RATE_LIMIT_SECONDS - (time() - $initialTime) + 1;
                    sleep($sleepDuration);
                    $initialTime = time();
                    $requests = 0;
                }
                $execute = $apiGateway->execute($json['links']['next']);
                $requests++;
            } else {
                break;
            }
        }
        return $collection;
    }

    /**
     * Get date (Y-m-d) for previous day
     *
     * @return string
     */
    protected function getPreviousDay()
    {
        return Carbon::now()->subDay()->format('Y-m-d');
    }

    /**
     * @param Response $response
     * @throws \Exception
     */
    protected function handleApiResponse(Response $response)
    {
        if ($response->getStatusCode() != 200) {
            throw new \Exception($response->getReasonPhrase(), $response->getStatusCode());
        }
    }

    /**
     * Check if given string is valid json
     *
     * @param $string
     * @return bool
     */
    protected function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * @param $projectCode
     * @return integer|null
     */
    protected function matchClient($projectCode, $harvestClientId)
    {
        // Try to find client by Harvest ID or organization number in database
        $clientRepository = app()->make(ClientInterface::class);
        $client = $clientRepository->model()
                       ->where('harvest_id', $harvestClientId)
                       ->orWhere('organization_number', $projectCode)
                       ->first();

        // If client is found with correct Harvest ID or organization number
        if ($client){
            if (! $client->harvest_id){
                $client->update([
                    'harvest_id'  => $harvestClientId,
                ]);
            }
            return $client->id;
        }

        // Return null on no match
        return null;
    }

    /**
     * @param $username
     * @return integer|null
     */
    protected function matchUser($username, $harvestUserId)
    {
        // Try to find user by Harvest ID or excact name match in database
        $userRepository = app()->make(UserInterface::class);
        $user = $userRepository->model()->where('harvest_id', $harvestUserId)
                               ->orWhere('name', $username)
                               ->first();

        // If client is found with correct Harvest ID or name
        if ($user){
            if (! $user->harvest_id){
                $user->update([
                    'harvest_id'  => $harvestUserId,
                ]);
            }
            return $user->id;
        }

        // Return null on no match
        return null;
    }
}
