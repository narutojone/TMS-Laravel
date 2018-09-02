<?php
 
namespace App\Repositories\TemplateNotification;

use App\Repositories\BaseEloquentRepository;
use App\Repositories\EmailTemplate\EmailTemplate;
use App\Repositories\GeneratedProcessedNotification\GeneratedProcessedNotificationInterface;
use App\Repositories\ProcessedNotification\ProcessedNotificationInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryTemplateNotification extends BaseEloquentRepository implements TemplateNotificationInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * EloquentRepositoryTemplateNotification constructor.
     *
     * @param \App\Repositories\TemplateNotification\TemplateNotification $model
     */
    public function __construct(TemplateNotification $model)
    {
        parent::__construct();

        $this->model = $model;
    }

    /**
     * Create a new TemplateNotification.
     *
     * @param array $input
     *
     * @return TemplateNotification
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
     * Update a TemplateNotification.
     *
     * @param integer $id
     * @param array $input
     *
     * @return \Illuminate\Database\Eloquent\Model
     * @throws ValidationException
     */
    public function update($id, array $input)
    {
        $input = $this->prepareUpdateData($input);

        if(!$this->isValid('update', $input)) {
            throw new ValidationException($this->validators['update']);
        }

        $templateNotification = $this->find($id);
        if ($templateNotification) {
            $templateNotification->fill($input);
            $templateNotification->save();
            return $templateNotification;
        }

        throw new ModelNotFoundException('Model TemplateNotification not found.', 404);
    }

    /**
     * Delete a TemplateNotification.
     *
     * @param integer $id
     *
     * @return void
     */
    public function delete($id)
    {
        $templateNotification = $this->model->find($id);
        if (!$templateNotification) {
            throw new ModelNotFoundException('Model TemplateNotification not found.', 404);
        }
        $templateNotification->delete();
    }

    /**
     * Check if a template notification was processed.
     *
     * @param int $taskId
     * @param int $templateNotificationId
     * @return bool
     */
    public function checkIfNotificationWasProcessed(int $taskId, int $templateNotificationId)
    {
        $processedNotificationRepository = app()->make(ProcessedNotificationInterface::class);

        $alreadyExists = false;

        $alreadyExists = $processedNotificationRepository->model()
            ->where('task_id', '=', $taskId)
            ->where('template_notification_id', '=', $templateNotificationId)
            ->first();

        if (!$alreadyExists) {
            $generatedTemplateNotificationRepository = app()->make(GeneratedProcessedNotificationInterface::class);
            $alreadyExists = $generatedTemplateNotificationRepository->model()
                ->where('task_id', '=', $taskId)
                ->where('template_notification_id', '=', $templateNotificationId)
                ->first();
        }



        return $alreadyExists;
    }

    /**
     * Prepare data for update action.
     *
     * @param array $data
     * @return array
     */
    protected function prepareUpdateData(array $data) : array
    {
        if ($data['type'] == 'template') {
            $emailTemplate = EmailTemplate::find($data['template']);

            $data['details'] = [
                'template' => $emailTemplate ? $emailTemplate->id : '',
                'data' => $data['vars'],
            ];
        } else {
            $data['details'] = [
                'message' => $data['message'],
            ];
        }

        if ($data['type'] != 'sms') {
            $data['details']['subject'] = $data['subject'];
        }

        return $data;
    }

    /**
     * Prepare data for create.
     *
     * @param array $data
     * @return array
     */
    protected function prepareCreateData(array $data) : array
    {
        if ($data['type'] == 'template') {
            $emailTemplate = EmailTemplate::find($data['template']);

            $data['details'] = [
                'template' => $emailTemplate ? $emailTemplate->id : '',
                'data' => $data['vars']
            ];
        } else {
            $data['details'] = [
                'message' => $data['message'],
            ];
        }

        if ($data['type'] != 'sms') {
            $data['details']['subject'] = $data['subject'];
        }

        return $data;
    }
}