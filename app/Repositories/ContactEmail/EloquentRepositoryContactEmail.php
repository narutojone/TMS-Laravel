<?php
 
namespace App\Repositories\ContactEmail;

use App\Repositories\BaseEloquentRepository;
use Huddle\Zendesk\Facades\Zendesk;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * The eloquent element in a repository should contain all data manipulation related to a entity
 */
class EloquentRepositoryContactEmail extends BaseEloquentRepository implements ContactEmailInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryContactEmail constructor.
     *
     * @param ContactEmail $model
     */
    public function __construct(ContactEmail $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new Contact Email.
     *
     * @param array $input
     *
     * @return ContactEmail
     * @throws ValidationException
     */
    public function create(array $input)
    {
        $input = $this->prepareCreateData($input);

        if(!$this->isValid('create', $input)) {
            throw new ValidationException($this->validators['create']);
        }

        $contactEmail = $this->model->create($input);

        $this->createZenDeskUser($contactEmail);

        return $contactEmail;
    }

    /**
     * Update a Contact Email.
     * NOT USED ANYMORE
     *
     * @param integer $id
     * @param array $input
     *
     * @return ContactEmail
     * @throws ValidationException
     */
    public function update($id, array $input)
    {
        return;
        $input = $this->prepareUpdateData($input);

        if(!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }

        $contactEmail = $this->find($id);
        if ($contactEmail) {
            $contactEmail->fill($input);
            $contactEmail->save();
            return $contactEmail;
        }

        throw new ModelNotFoundException('Model ContactEmail not found.', 404);
    }

    /**
     * Delete a contact email
     *
     * @param $id
     * @return void
     * @throws \Exception
     */
    public function delete($id)
    {
        $contactEmail = $this->model->find($id);
        if (!$contactEmail) {
            throw new ModelNotFoundException('Model ContactEmail not found.', 404);
        }

        DB::beginTransaction();
        try {
            // Mark another email as primary if current one which is going to be deleted is primary
            // Choose the first added after this one
            if ($contactEmail->isPrimary()) {
                $nextPrimary = $this->model()->where('contact_id', $contactEmail->contact_id)->where('id', '<>',$contactEmail->id)->orderBy('id', 'ASC')->first();
                if($nextPrimary) {
                    $nextPrimary->update([
                        'primary' => 1,
                    ]);
                }
            }

            // Delete the contact phone number
            $contactEmail->delete();
            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Mark an email as primary
     *
     * @param $id
     * @throws \Exception
     */
    public function setPrimary($id)
    {
        $contactEmail = $this->model->find($id);
        if (!$contactEmail) {
            throw new ModelNotFoundException('Model ContactEmail not found.', 404);
        }

        if(!$contactEmail->isPrimary()) {
            DB::beginTransaction();
            try{
                // Mark the old primary as non primary
                $this->model()->where('contact_id', $contactEmail->contact_id)->where('primary', 1)->update([
                    'primary' => 0,
                ]);

                // Set the current email as primary
                $contactEmail->update([
                    'primary' => 1,
                ]);
                DB::commit();
            }
            catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        }
    }

    /**
     * Prepare data for create action
     *
     * @param $input
     * @return array
     */
    protected function prepareCreateData(array $input) : array
    {
        // Set primary field data
        if(!isset($input['primary'])) {
            $input['primary'] = 0;
        }

        // Set as primary if there are no other emails associated with this contact
        $existingPrimary = $this->model->where('contact_id', $input['contact_id'])->where('primary', 1)->count();
        if(!$existingPrimary) {
            $input['primary'] = 1;
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
        return $input;
    }

    protected function createZenDeskUser(ContactEmail $contactEmail)
    {
        $attributes = [
            'name'              => $contactEmail->contact->name,
            'organization_id'   => $contactEmail->contact->clients->first()->zendesk_id,
            'phone'             => $contactEmail->contact->phones->count() ? $contactEmail->contact->phones->first()->number : '',
            'email'             => $contactEmail->address,
        ];

        // Search for the current customer
        $params = array('query' => $contactEmail->address);

        try {
            $search = Zendesk::users()->search($params);
        }
        catch (\Exception $e) {
            return;
        }

        if (empty($search->users)) {
            try {
                $response = Zendesk::users()->create($attributes);
                $contactEmail->update([
                    'zendesk_id' => $response->user->id,
                ]);
            } catch(\Exception $e) {
                // TODO(alex) do something in case of error
            }
        }
        else {
            try {
                $response = Zendesk::users()->update($search->users[0]->id, $attributes);
            }
            catch(\Exception $e) {
                // TODO(alex) do something in case of error
            }
        }
    }
}