<?php

namespace App\Repositories\Rating;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Repositories\BaseEloquentRepository;
use App\Repositories\Rating\Rating;
use App\Repositories\Rating\RatingInterface;
use App\Repositories\BaseRepositoriesInterface;
use App\Repositories\Task\TaskInterface;
use App\Repositories\Task\Task;
use App\Repositories\Comment\CommentInterface;
use App\Repositories\Option\OptionInterface;
use Illuminate\Validation\ValidationException;
use App\Repositories\GroupUser\GroupUserInterface;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * THe eloquent element in a repository should contain all data manipulation related to a enetity
 */
class EloquentRepositoryRating extends BaseEloquentRepository implements RatingInterface
{
    /**
     * @var $model
     */
    protected $model;

    /**
     * @var $taskRepository - EloquentRepositoryTask
     */
    protected $taskRepository;

    /**
     * @var $commentRepository - EloquentRepositoryComment
     */
    private $commentRepository;

    /**
     * EloquentRepositoryRating constructor.
     *
     * @param App\Respositories\Rating\Rating $model
     */
    public function __construct(Rating $model)
    {
        parent::__construct();

        $this->model = $model;
        $this->taskRepository = app()->make(TaskInterface::class);
        $this->commentRepository = app()->make(commentInterface::class);
    }
    
    /**
     * Create a new Rating.
     *
     * @param array $input
     *
     * @return App\Respositories\Rating\Rating
     */
    public function create(array $input)
    {
        if(!$this->isValid('create', $input)) {
            throw new HttpResponseException(response()->json($this->errors, 422));
        }
        
        DB::beginTransaction();

        try {
            $rating = $this->model->create($input);

            if ($rating->rate <= 2) {
                $optionRepository = app()->make(OptionInterface::class);
                $groupUserRepository = app()->make(GroupUserInterface::class);
                
                // Always default task to not private
                $private = Task::NOT_PRIVATE;

                if ($rating->ratingable_type == 'App\Repositories\User\User') {
                    $templateOptionKey = 'user_rating_task_template_bad_rating';
                    $groupOptionKey = 'user_rating_task_group';
                    $deadlineOptionKey = 'user_rating_task_deadline';
                    $client = $rating->commentable()->first();
                    $private = Task::PRIVATE;
                } elseif ($rating->ratingable_type == 'App\Repositories\Client\Client') {
                    $templateOptionKey = 'client_rating_task_template_bad_rating';
                    $groupOptionKey = 'client_rating_task_group';
                    $deadlineOptionKey = 'client_rating_task_deadline';
                    $client = $rating->ratingable()->first();
                }

                // Find template for task on paused client
                $template = $optionRepository->model()->where('key', '=', $templateOptionKey)->first()->template;

                // Find user from group
                $group = $optionRepository->model()->where('key', '=', $groupOptionKey)->first()->group;
                $userId = null;

                if ($group) {
                    $groupUser = $groupUserRepository->model()->where('group_id', $group->id)->inRandomOrder()->first();

                    if ($groupUser) {
                        $userId = $groupUser->user_id;
                    }
                }

                // Find deadline days
                $deadlineOption = $optionRepository->model()->where('key', '=', $deadlineOptionKey)->first();

                // Create task
                $task = $this->taskRepository->create([
                    'user_id'     => $userId,
                    'client_id'   => $client->id,
                    'template_id' => $template->id,
                    'private'     => $private,
                    'active'      => Task::ACTIVE,
                    'repeating'   => Task::NOT_REPEATING,
                    'deadline'    => Carbon::now()->addDays($deadlineOption->value)->format('Y-m-d H:i:s'),
                ]);

                if($task && $task->user_id && $rating->feedback){
                    $comment = 'Automated comment: The client gave us ' . $rating->rate . '/5 with the following comment: ' . $rating->feedback;

                    $this->commentRepository->create([
                        'user_id'           => $task->user_id,
                        'task_id'           => $task->id,
                        'comment'           => $comment,
                        'after_complete'    => false,
                        'from_review_page'  => false,
                    ]);
                }
            }
        }
        catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }

        DB::commit();
        
        return $rating;
    }
 
    /**
     * Update a Rating.
     *
     * @param integer $id
     * @param array $attributes
     *
     * @return App\Respositories\Rating\Rating
     */
    public function update($id, array $attributes)
    {
        if(!$this->isValid('update', $input)) {
            throw new HttpResponseException(response()->json($this->errors, 422));
        }
        
        $rating = $this->find($input['id']);
        if ($rating) {
            $rating->fill($input);
            $rating->save();
            return $rating;
        }
        
        throw new HttpResponseException(response()->json(['Model Rating not found.'], 404));
    }
 
    /**
     * Delete a Rating.
     *
     * @param integer $id
     *
     * @return boolean
     */
    public function delete($id)
    {
        $rating = $this->model->find($id);
        if (!$rating) {
            throw new HttpResponseException(response()->json(['Model Rating not found.'], 404));
        }
        $rating->delete();
    }

    /**
     * Get average rating for last one hundred rows
     *
     * @return float
     */
    public function getAverageRating()
    {
        $average = $this->model()
            ->select('rate')
            ->where('commentable_type', 'App\Repositories\Client\Client')
            ->latest()
            ->limit(100)
            ->get()
            ->sum('rate');
        
        return new Rating(['rate' => number_format($average, 2) / 100]);
    }
}