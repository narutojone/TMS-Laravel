<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Client\Client;
use App\Repositories\Client\ClientCreateRequest;
use App\Repositories\Client\ClientInterface;
use App\Repositories\Client\ClientTransformer;
use App\Repositories\Client\ClientUpdateRequest;
use App\Repositories\ClientPhone\ClientPhone;


class ClientController extends Controller
{
    /**
     * @var $clientRepository - EloquentRepositoryClient
     */
    private $clientRepository;

    /**
     * ClientController constructor.
     *
     * @param ClientInterface $clientRepository
     */
    public function __construct(ClientInterface $clientRepository)
    {
        parent::__construct();

        $this->clientRepository = $clientRepository;
    }

    /**
     * Update a client
     *
     * @param ClientUpdateRequest $request
     * @param Client $client
     *
     * @return \Illuminate\Http\Response
     */
    public function update(ClientUpdateRequest $request, Client $client)
    {
        // Authorize request
        $this->authorize('update', $client);

        $client = $this->clientRepository->update($client->id, $request->all());

        return (new ClientTransformer)->transform($client);
    }

    /**
     * Create a client
     *
     * @param ClientCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClientCreateRequest $request)
    {
        // Authorize request
        $this->authorize('create',Client::class);

        $client = $this->clientRepository->create($request->all());

        return (new ClientTransformer)->transform($client);
    }
}
