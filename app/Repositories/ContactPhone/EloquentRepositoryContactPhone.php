<?php
 
namespace App\Repositories\ContactPhone;

use App\Repositories\BaseEloquentRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * The eloquent element in a repository should contain all data manipulation related to a entity
 */
class EloquentRepositoryContactPhone extends BaseEloquentRepository implements ContactPhoneInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryContactPhone constructor.
     *
     * @param ContactPhone $model
     */
    public function __construct(ContactPhone $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new Contact Phone.
     *
     * @param array $input
     *
     * @return ContactPhone
     * @throws ValidationException
     */
    public function create(array $input)
    {
        $input = $this->prepareCreateData($input);

        if(!$this->isValid('create', $input)) {
            throw new ValidationException($this->validators['create']);
        }

        return $this->model->create($input);
    }

    /**
     * Update a Contact Phone.
     *
     * @param integer $id
     * @param array $input
     *
     * @return ContactPhone
     * @throws ValidationException
     */
    public function update($id, array $input)
    {
        $input = $this->prepareUpdateData($input);

        if(!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }

        $contactPhone = $this->find($id);
        if ($contactPhone) {
            $contactPhone->fill($input);
            $contactPhone->save();
            return $contactPhone;
        }

        throw new ModelNotFoundException('Model ContactPhone not found.', 404);
    }

    /**
     * Delete a contact phone
     *
     * @param $id
     * @return void
     * @throws \Exception
     */
    public function delete($id)
    {
        $contactPhone = $this->model->find($id);
        if (!$contactPhone) {
            throw new ModelNotFoundException('Model ContactPhone not found.', 404);
        }

        DB::beginTransaction();
        try {
            // Mark another phone as primary if current one which is going to be deleted is primary
            // Choose the first added after this one
            if ($contactPhone->isPrimary()) {
                $nextPrimary = $this->model()->where('contact_id', $contactPhone->contact_id)->where('id', '<>',$contactPhone->id)->orderBy('id', 'ASC')->first();
                if($nextPrimary) {
                    $nextPrimary->update([
                        'primary' => 1,
                    ]);
                }
            }

            // Delete the contact phone number
            $contactPhone->delete();
            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Mark a phone number as primary
     *
     * @param $id
     * @throws \Exception
     */
    public function setPrimary($id)
    {
        $contactPhone = $this->model->find($id);
        if (!$contactPhone) {
            throw new ModelNotFoundException('Model ContactPhone not found.', 404);
        }

        if(!$contactPhone->isPrimary()) {
            DB::beginTransaction();
            try{
                // Mark the old primary as non primary
                $this->model()->where('contact_id', $contactPhone->contact_id)->where('primary', 1)->update([
                    'primary' => 0,
                ]);

                // Set the current phone number as primary
                $contactPhone->update([
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

        // Set as primary if there are no other phone numbers associated with this contact
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
}