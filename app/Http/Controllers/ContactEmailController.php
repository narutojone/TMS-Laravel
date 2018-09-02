<?php

namespace App\Http\Controllers;

use App\Repositories\Client\Client;
use App\Repositories\Contact\Contact;
use App\Repositories\ContactEmail\ContactEmail;
use App\Repositories\ContactEmail\ContactEmailCreateRequest;
use App\Repositories\ContactEmail\ContactEmailInterface;
use App\Repositories\ContactEmail\ContactEmailUpdateRequest;
use Illuminate\Http\Request;

class ContactEmailController extends Controller
{
    /**
     * @var $contactEmailRepository - EloquentRepositoryContactEmail
     */
    private $contactEmailRepository;

    /**
     * ContactEmailController constructor.
     *
     * @param ContactEmailInterface $contactEmailRepository
     */
    public function __construct(ContactEmailInterface $contactEmailRepository)
    {
        parent::__construct();

        $this->contactEmailRepository = $contactEmailRepository;
    }

    /**
     * Show the create form for a contact email
     *
     * @param Request $request
     * @param Client $client
     * @param Contact $contact
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request, Client $client, Contact $contact)
    {
        return view('contacts.emails.create')->with([
            'contact' => $contact,
            'client'  => $client,
        ]);
    }

    /**
     * Show the edit form for a contact email
     *
     * @param Request $request
     * @param Contact $contact
     * @param ContactEmail $email
     * @return $this
     */
    public function edit(Request $request, Contact $contact, ContactEmail $email)
    {
        return view('contacts.emails.edit')->with([
            'contact' => $contact,
            'email'   => $email,
        ]);
    }

    /**
     * Update a contact email
     *
     * @param ContactEmailUpdateRequest $request
     * @param int $contact
     * @param int $email
     * @return \Illuminate\Http\RedirectResponse
     *
     */
    public function update(ContactEmailUpdateRequest $request, int $contact, int $email)
    {
        $this->contactEmailRepository->update($email, $request->all());

        return redirect()->route('contacts.edit', $contact)->with('success', 'Contact email updated.');
    }

    /**
     * Store a new contact email
     *
     * @param ContactEmailCreateRequest $request
     * @param Client $client
     * @param Contact $contact
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ContactEmailCreateRequest $request, Client $client, Contact $contact)
    {
        $contactEmailData = $request->all();
        $contactEmailData['contact_id'] = $contact->id;

        $this->contactEmailRepository->create($contactEmailData);

        return redirect()->route('client.contact.edit', [$client, $contact])->with('success', 'Contact email address saved.');
    }

    /**
     * Delete a contact email
     *
     * @param Request $request
     * @param Client $client
     * @param Contact $contact
     * @param ContactEmail $email
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request, Client $client, Contact $contact, ContactEmail $email)
    {
        $this->contactEmailRepository->delete($email->id);

        return redirect()->route('client.contact.edit', [$client, $contact])->with('success', 'Email address number deleted');
    }

    /**
     * Mark an email as primary
     *
     * @param Request $request
     * @param Client $client
     * @param Contact $contact
     * @param ContactEmail $email
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setPrimary(Request $request, Client $client, Contact $contact, ContactEmail $email)
    {
        $this->contactEmailRepository->setPrimary($email->id);

        return redirect()->route('client.contact.edit', [$client, $contact])->with('success', 'Primary email changed');
    }
}
