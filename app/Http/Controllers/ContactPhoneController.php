<?php

namespace App\Http\Controllers;

use App\Repositories\Client\Client;
use App\Repositories\Contact\Contact;
use App\Repositories\ContactPhone\ContactPhone;
use App\Repositories\ContactPhone\ContactPhoneCreateRequest;
use App\Repositories\ContactPhone\ContactPhoneInterface;
use App\Repositories\ContactPhone\ContactPhoneUpdateRequest;
use Illuminate\Http\Request;

class ContactPhoneController extends Controller
{
    /**
     * @var $contactPhoneRepository - EloquentRepositoryContactPhone
     */
    private $contactPhoneRepository;

    /**
     * ContactPhoneController constructor.
     *
     * @param ContactPhoneInterface $contactPhoneRepository
     */
    public function __construct(ContactPhoneInterface $contactPhoneRepository)
    {
        parent::__construct();

        $this->contactPhoneRepository = $contactPhoneRepository;
    }

    /**
     * Show the create form for a contact phone
     *
     * @param Request $request
     * @param Client $client
     * @param Contact $contact
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request, Client $client, Contact $contact)
    {
        return view('contacts.phones.create')->with([
            'contact' => $contact,
            'client'  => $client,
        ]);
    }

    /**
     * Show the edit form for a contact phone
     *
     * @param Request $request
     * @param Contact $contact
     * @param ContactPhone $phone
     * @return $this
     */
    public function edit(Request $request, Contact $contact, ContactPhone $phone)
    {
        return view('contacts.phones.edit')->with([
            'contact' => $contact,
            'phone'   => $phone,
        ]);
    }

    /**
     * Update a contact phone
     *
     * @param ContactPhoneUpdateRequest $request
     * @param int $contact
     * @param int $phone
     * @return \Illuminate\Http\RedirectResponse
     *
     */
    public function update(ContactPhoneUpdateRequest $request, int $contact, int $phone)
    {
        $this->contactPhoneRepository->update($phone, $request->all());

        return redirect()->route('contacts.edit', $contact)->with('success', 'Contact phone updated.');
    }

    /**
     * Store a new contact
     *
     * @param ContactPhoneCreateRequest $request
     * @param Contact $contact
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ContactPhoneCreateRequest $request, Client $client, Contact $contact)
    {
        $contactPhoneData = $request->all();
        $contactPhoneData['contact_id'] = $contact->id;

        $this->contactPhoneRepository->create($contactPhoneData);

        return redirect()->route('client.contact.edit', [$client, $contact])->with('success', 'Contact phone saved.');
    }

    /**
     * Delete a contact phone number
     *
     * @param Request $request
     * @param Client $client
     * @param Contact $contact
     * @param ContactPhone $phone
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request, Client $client, Contact $contact, ContactPhone $phone)
    {
        $this->contactPhoneRepository->delete($phone->id);

        return redirect()->route('client.contact.edit', [$client, $contact])->with('success', 'Phone number deleted');
    }

    /**
     * Mark a phone number as primary
     *
     * @param Request $request
     * @param Contact $contact
     * @param ContactPhone $phone
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setPrimary(Request $request, Client $client, Contact $contact, ContactPhone $phone)
    {
        $this->contactPhoneRepository->setPrimary($phone->id);

        return redirect()->route('client.contact.edit', [$client, $contact])->with('success', 'Primary phone number changed');
    }
}
