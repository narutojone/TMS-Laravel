<?php
 
namespace App\Repositories\Contact;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\Client\Client;
use App\Repositories\Client\ClientInterface;
use App\Repositories\ContactEmail\ContactEmail;
use App\Repositories\ContactEmail\ContactEmailInterface;
use App\Repositories\ContactPhone\ContactPhone;
use App\Repositories\ContactPhone\ContactPhoneInterface;
use Huddle\Zendesk\Facades\Zendesk;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * The eloquent element in a repository should contain all data manipulation related to a entity
 */
class EloquentRepositoryContact extends BaseEloquentRepository implements ContactInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryContact constructor.
     *
     * @param Contact $model
     */
    public function __construct(Contact $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new Contact.
     *
     * @param array $input
     * @return Contact
     * @throws ValidationException
     * @throws \Exception
     */
    public function create(array $input)
    {
        $input = $this->prepareCreateData($input);

        if(!$this->isValid('create', $input)) {
            throw new ValidationException($this->validators['create']);
        }

        DB::beginTransaction();

        try {
            $contactEmail = $contactPhone = null;

            // Find the client
            $clientRepository = app()->make(ClientInterface::class);
            $client = $clientRepository->find($input['client_id']);

            // Store the contact person
            $contact = $this->model->create($input);

            // Attach contact person to client
            $clientRepository->linkContact($client->id, [
                'contact_id' => $contact->id,
                'primary'    => $input['primary'],
            ]);

            // Create contact email record
            $contactEmailRepository = app()->make(ContactEmailInterface::class);
            $contactEmail = $contactEmailRepository->create([
                'contact_id' => $contact->id,
                'address' => $input['address'],
                'primary' => 1,
            ]);

            if (isset($input['number'])) {
                $contactPhoneRepository = app()->make(ContactPhoneInterface::class);
                $contactPhone = $contactPhoneRepository->create([
                    'contact_id' => $contact->id,
                    'number' => $input['number'],
                    'primary' => 1,
                ]);
            }
        }
        catch (\Exception $e) {
            DB::rollback();
            throw  $e;
        }

        DB::commit();

        return $contact;
    }

    /**
     * Update a Contact.
     *
     * @param integer $id
     * @param array $input
     * @throws ValidationException
     * @throws \Exception
     * @return Contact
     */
    public function update($id, array $input)
    {
        $input = $this->prepareUpdateData($input);

        if(!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }

        $contact = $this->find($id);
        if($contact) {
            // Update the contact person main details
            $contact->fill($input);
            $contact->save();
            return $contact;
        }

        throw new ModelNotFoundException('Model Contact not found.', 404);

    }

    /**
     * Deactivate a contact
     *
     * @param $id
     * @return void
     */
    public function deactivate($id)
    {
        $contact = $this->model->find($id);
        if (!$contact) {
            throw new ModelNotFoundException('Model Contact not found.', 404);
        }
        $contact->update([
            'active' => 0,
        ]);
    }

    /**
     * Prepare data for create action
     *
     * @param $input
     * @return array
     */
    protected function prepareCreateData(array $input) : array
    {
        if(!isset($input['active'])) {
            $input['active'] = Contact::ACTIVE;
        }
        if(!isset($input['notes']) || is_null($input['notes'])) {
            $input['notes'] = '';
        }

        return $input;
    }

    /**
     * Prepare data for update action
     *
     * @param $input
     * @return array
     */
    protected function prepareUpdateData(array $input) : array
    {
        if(!isset($input['phone'])) {
            $input['phone'] = [];
        }
        if(is_null($input['notes'])) {
            $input['notes'] = '';
        }

        return $input;
    }

    /**
     * Store phone numbers for a new contact
     *
     * @param int $contactId
     * @param array $phoneNumbers
     */
    protected function storePhones(int $contactId, array $phoneNumbers)
    {
        $contactPhoneRepository = app()->make(ContactPhoneInterface::class);
        foreach ($phoneNumbers as $phoneIndex => $phoneNumber) {
            $contactPhoneRepository->create([
                'contact_id' => $contactId,
                'number'     => $phoneNumber,
                'primary'    => ($phoneIndex == 0) ? 1 : 0, // first phone number will be marked as primary
            ]);
        }
    }

    /**
     * Store email addresses for a new contact
     *
     * @param int $contactId
     * @param array $emailAddresses
     */
    protected function storeEmails(int $contactId, array $emailAddresses)
    {
        $contactEmailRepository = app()->make(ContactEmailInterface::class);
        foreach ($emailAddresses as $emailIndex => $emailAddress) {
            $contactEmailRepository->create([
                'contact_id' => $contactId,
                'address'    => $emailAddress,
                'primary'    => ($emailIndex == 0) ? 1 : 0, // first email address will be marked as primary
            ]);
        }
    }

    /**
     * Update all phones for a certain contact person.
     * Update is made by deleting all existing phones and inserting new ones.
     *
     * @param int $contactId
     * @param array $phoneNumbers
     */
    protected function updatePhones(int $contactId, array $phoneNumbers)
    {
        // Delete old phones
        $this->model->find($contactId)->phones()->delete();

        // Store new phones
        $this->storePhones($contactId, $phoneNumbers);
    }

    /**
     * Update all emails for a certain contact person.
     * Update is made by deleting all existing emails and inserting new ones.
     *
     * @param int $contactId
     * @param array $emailAddresses
     */
    protected function updateEmails(int $contactId, array $emailAddresses)
    {
        // Delete old email addresses
        $this->model->find($contactId)->emails()->delete();

        // Store new email addresses
        $this->storeEmails($contactId, $emailAddresses);
    }

}