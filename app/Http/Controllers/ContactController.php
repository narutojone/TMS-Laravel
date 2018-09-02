<?php

namespace App\Http\Controllers;

use App\Repositories\Client\Client;
use App\Repositories\Client\ClientInterface;
use App\Repositories\Contact\Contact;
use App\Repositories\Contact\ContactCreateRequest;
use App\Repositories\Contact\ContactInterface;
use App\Repositories\Contact\ContactUpdateRequest;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * @var $contactRepository - EloquentRepositoryContact
     */
    private $contactRepository;

    /**
     * @var $clientRepository - EloquentRepositoryClient
     */
    private $clientRepository;

    /**
     * ContactController constructor.
     *
     * @param ContactInterface $contactRepository
     * @param ContactInterface $clientRepository
     */
    public function __construct(ContactInterface $contactRepository, ClientInterface $clientRepository)
    {
        parent::__construct();

        $this->contactRepository = $contactRepository;
        $this->clientRepository = $clientRepository;
    }

    /**
     * Show a list of contacts
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $contacts = $this->contactRepository->make()->orderBy('name', 'ASC')->get();

        return view('contacts.index')->with([
            'contacts' => $contacts,
        ]);
    }

    /**
     * Show the create form for a contact
     *
     * @param Request $request
     * @param Client $client
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request, Client $client)
    {
        // Authorize request
        $this->authorize('create', [Contact::class, $client]);

        return view('contacts.create')->with([
            'client' => $client,
        ]);
    }

    /**
     * Show the edit form for a contact
     *
     * @param Request $request
     * @param int $clientId - Client ID
     * @param int $id - Contact ID
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, int $clientId, int $id)
    {
        $contact = $this->contactRepository->find($id);
        $client = $this->clientRepository->find($clientId);

        return view('contacts.edit')->with([
            'contact' => $contact,
            'client'  => $client,
        ]);
    }

    /**
     * Update a contact
     *
     * @param ContactUpdateRequest $request
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ContactUpdateRequest $request, int $id)
    {
        $this->contactRepository->update($id, $request->all());

        $client = $this->clientRepository->model()->find($request->get('client_id'));

        return redirect()->route('client.contacts.index', $client)->with('success', 'Contact updated.');
    }

    /**
     * Show a contact
     *
     * @param ContactUpdateRequest $request
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show(ContactUpdateRequest $request, int $id)
    {
        $contact = $this->contactRepository->make()->find($id);

        return view('contacts.show')->with([
            'contact' => $contact,
        ]);
    }

    /**
     * Store a new contact
     *
     * @param ContactCreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ContactCreateRequest $request)
    {
        $clientRepository = app()->make(ClientInterface::class);
        $client = $clientRepository->find($request->get('client_id'));

        // Authorize request
        $this->authorize('create', [Contact::class, $client]);

        $this->contactRepository->create($request->all());

        return redirect()->route('client.contacts.index', $client)->with('success', 'Contact created.');
    }

    /**
     * Deactivate a contact person
     *
     * @param Request $request
     * @param Contact $contact
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deactivate(Request $request, Contact $contact)
    {
        $this->contactRepository->deactivate($contact->id);

        return redirect()->route('contacts.index')->with('success', 'Contact deactivated.');
    }
}
