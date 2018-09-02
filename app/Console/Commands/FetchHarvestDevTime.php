<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use GuzzleHttp\Psr7\Response;
use Illuminate\Console\Command;
use App\Services\HarvestApiGateway;
use App\Repositories\HarvestDevTimeEntry\HarvestDevTimeEntryInterface;

class FetchHarvestDevTime extends Command
{
    CONST RATE_LIMIT_REQUESTS = 100;
    CONST RATE_LIMIT_SECONDS = 15;

    protected $signature = 'harvest:fetch-dev-time {--mode=daily}';
    protected $description = 'Fetch and assign time from Harvest API';

    protected $harvestDevTimeEntryRepository;

    public function __construct(HarvestDevTimeEntryInterface $harvestDevTimeEntryRepository)
    {
        parent::__construct();

        $this->harvestDevTimeEntryRepository = $harvestDevTimeEntryRepository;
    }

    public function handle()
    {
        $mode = $this->option('mode');
        $timeEntries = $this->getAllTimeEntries($mode);

        if ($timeEntries) {
            foreach ($timeEntries as $timeEntry) {

                $hours = $this->makeHours($timeEntry['hours']);

                $find = $this->harvestDevTimeEntryRepository->model()->where('external_id', $timeEntry['external_id'])->first();
                if ($find) {
                    $this->harvestDevTimeEntryRepository->update($find['id'], [
                        'github_issue' => $timeEntry['issue_number'],
                        'tracked_time' => $hours,
                        'spent_date' => $timeEntry['spent_date'],
                        'notes' => $timeEntry['notes'],
                    ]);
                } else {
                    $this->harvestDevTimeEntryRepository->create([
                        'external_id' => $timeEntry['external_id'],
                        'github_issue' => $timeEntry['issue_number'],
                        'username' => $timeEntry['username'],
                        'tracked_time' => $hours,
                        'spent_date' => $timeEntry['spent_date'],
                        'notes' => $timeEntry['notes'],
                    ]);
                }
            }
        }
    }

    /**
     * Get all time entries
     *
     * @param $mode
     * @return array
     * @throws \Exception
     */
    protected function getAllTimeEntries($mode)
    {
        $harvest = app()->make('harvest');

        $collection = [];
        $initialTime = time();
        $requests = 1;

        if ($mode == 'all') {
            $apiResponse = $harvest->timeEntry->get();

            while (true) {

                $toJson = $apiResponse->toJson();
                if (array_key_exists('error', $toJson)) {
                    throw new \Exception($toJson['error'], 401);
                }

                $entries = $toJson['time_entries'];

                $collection = array_merge($collection, array_map(function ($item) {

                    $notes = $item['notes'];
                    $issue_number = $this->findIssueNumber($notes);
                    $attributes = [
                        'external_id' => $item['id'],
                        'hours' => $this->makeHours($item['hours']),
                        'notes' => $notes,
                        'issue_number' => $issue_number,
                        'spent_date' => $item['spent_date'],
                    ];

                    if (isset($item['user']['name'])) {
                        $attributes['username'] = $item['user']['name'];
                    }

                    return $attributes;

                }, $entries));

                // Go to the next page if it exists
                if (!is_null($toJson['next_page'])) {
                    if ($requests == self::RATE_LIMIT_REQUESTS) {
                        $sleepDuration = self::RATE_LIMIT_SECONDS - (time() - $initialTime) + 1;
                        sleep($sleepDuration);
                        $initialTime = time();
                        $requests = 0;
                    }
                    $apiResponse = $apiResponse->next();
                    $requests++;
                } else {
                    break;
                }
            }

        } else {

            $previousDay = $this->getPreviousDay();

            $path = 'https://api.harvestapp.com/v2/time_entries?from=' . $previousDay;
            $apiGateway = new HarvestApiGateway();
            $execute = $apiGateway->execute($path);
            /**
             * @var Response $response
             */
            $response = $execute->response;

            $this->handleAPIResponse($response);

            while (true) {
                $json = $execute->json();
                $collection = array_merge($collection, collect($json['time_entries'])->map(function ($item) {

                    $notes = $item['notes'];
                    $issue_number = $this->findIssueNumber($notes);

                    $attributes = [
                        'external_id' => $item['id'],
                        'hours' => $this->makeHours($item['hours']),
                        'notes' => $notes,
                        'issue_number' => $issue_number,
                        'spent_date' => $item['spent_date'],
                    ];

                    if (isset($item['user']['name'])) {
                        $attributes['username'] = $item['user']['name'];
                    }

                    return $attributes;

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

        }

        return $collection;
    }

    /**
     * Get date (Y-m-d) for previous day
     *
     * @return mixed
     */
    protected function getPreviousDay()
    {
        return Carbon::now()->subDay()->format('Y-m-d');
    }

    /**
     * Try to find and return issue number from notes by specified pattern
     *
     * @param $source
     * @return bool|mixed
     */
    protected function findIssueNumber($source)
    {
        $matches = [];
        preg_match('/#(\d+)/', $source, $matches);

        if ($matches && count($matches) == 2 && isset($matches[1])) {
            return $matches[1];
        }
        return null;
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
     * @param $hours
     * @return int
     */
    protected function makeHours(float $hours)
    {
        return ($hours > 0) ? $hours : 0;
    }
}
