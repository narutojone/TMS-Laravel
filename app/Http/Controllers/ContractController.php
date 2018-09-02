<?php

namespace App\Http\Controllers;

use App\Helpers\ContractUtils;
use App\Repositories\Client\Client;
use App\Repositories\Client\ClientInterface;
use App\Repositories\Contract\Contract;
use App\Repositories\Contract\ContractCreateRequest;
use App\Repositories\Contract\ContractInterface;
use App\Repositories\ContractSalaryDay\ContractSalaryDay;
use App\Repositories\ContractSalaryDay\ContractSalaryDayInterface;
use App\Repositories\Template\TemplateInterface;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    /**
     * @var $contractRepository - EloquentRepositoryContract
     */
    private $contractRepository;

    /**
     * @var $contractSalaryDayRepository - EloquentRepositoryContractSalaryDay
     */
    private $contractSalaryDayRepository;

    /**
     * @var $clientRepository - EloquentRepositoryClient
     */
    private $clientRepository;

    /**
     * ContractController constructor.
     *
     * @param ContractInterface $contractRepository
     * @param ClientInterface $clientRepository
     */
    public function __construct(ContractInterface $contractRepository, ContractSalaryDayInterface $contractSalaryDayRepository, ClientInterface $clientRepository)
    {
        parent::__construct();

        $this->contractRepository = $contractRepository;
        $this->contractSalaryDayRepository = $contractSalaryDayRepository;
        $this->clientRepository = $clientRepository;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @param Client $client
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, Client $client, $type = 'simple')
    {
        // Authorize request
        $this->authorize('create', [Contract::class, $client]);

        $salaryDays = $this->contractSalaryDayRepository->generateAllSalaryDays();

        // New contracts should be initially filled with old contract attributes
        // If no contract has been previously created we pass an empty object(to simplify view)
        $oldContract = $client->contracts()->firstOrNew(['client_id' => $client->id]);

        // Return to view $type with data
        return view('contracts.create.' . $type)->with([
            'client'      => $client,
            'salaryDays'  => $salaryDays,
            'oldContract' => $oldContract,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Repositories\Contract\ContractCreateRequest
     *
     * @return \Illuminate\Http\Response
     */
    public function store(ContractCreateRequest $request)
    {
        // Authorize request
        $client = $this->clientRepository->find($request->get('client_id'));
        $this->authorize('create', [Contract::class, $client]);

        $contract = $this->contractRepository->create($request->all());

        return redirect()
            ->action('ClientController@show', $client)
            ->with('success', 'Contract created.');
    }

    public function preview(ContractCreateRequest $request)
    {
        // Authorize request
        $client = $this->clientRepository->find($request->get('client_id'));
        $this->authorize('create', [Contract::class, $client]);

        $contractUtils = new ContractUtils();
        $contractUtils->setContract($request->all());

        $newTasksList = $contractUtils->generateNewContractTaskList();

        $templatesUsedForNewTasks = [];
        $templateRepository = app()->make(TemplateInterface::class);
        if(isset($newTasksList['newTasks']) && !empty($newTasksList['newTasks'])) {
            foreach ($newTasksList['newTasks'] as $newTask) {
                $templatesUsedForNewTasks[] = $newTask['template'];
            }
        }
        if(isset($newTasksList['tasksToBeUpdated']) && !empty($newTasksList['tasksToBeUpdated'])) {
            foreach ($newTasksList['tasksToBeUpdated'] as $newTask) {
                $templatesUsedForNewTasks[] = $newTask['template'];
            }
        }
        $templates = $templateRepository->model()->whereIn('id', $templatesUsedForNewTasks)->get()->toArray();
        $templatesUsedForNewTasks = [];
        foreach ($templates as $template) {
            $templatesUsedForNewTasks[$template['id']] = $template;
        }

        return view('contracts.preview')->with([
            'client'     => $client,
            'contract'   => $request->all(),
            'tasks'      => $newTasksList,
            'templates'  => $templatesUsedForNewTasks,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Repositories\Contract\Contract  $contract
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Contract $contract)
    {
        // Authorize request
        $this->authorize('show', Contract::class);

        return view('contracts.show')->with([
            'contract'  => $contract,
        ]);
    }

    /**
     * Terminate an existing active contract
     *
     * @param Request $request
     * @param Contract $contract
     * @return \Illuminate\Http\RedirectResponse
     */
    public function terminate(Request $request, Contract $contract)
    {
        // Authorize request
        $this->authorize('terminate', Contract::class);

        // Terminate active contract
        $terminatedContract = $this->contractRepository->terminate($contract);

        return redirect()
            ->action('ContractController@show', $terminatedContract)
            ->with('success', 'Contract terminated.');
    }

}
