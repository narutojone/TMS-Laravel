@extends('layouts.app')

@section('title', 'Review Task')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-6">
            <h2>Reviews</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                    <a href="{{ action('ReviewsController@pending') }}">Reviews</a>
                </li>
                <li>
                    <a href="{{ url('/reviews/'.$review->id) }}">Review for {{ $review->userReviewed->name }}</a>
                </li>
                <li class="active">
                    Task - {{ $task->title }}
                </li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="wrapper wrapper-content">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-lg-5">
                                <dl class="dl-horizontal m-b-none">
                                    <dt>Client:</dt> <dd><a href="{{ action('ClientController@show', $task->client) }}">{{ $task->client->name }}</a></dd>
                                    <dt>Deadline:</dt> <dd>@date($task->deadline)</dd>

                                    @if ($task->isComplete())
                                        <dt>Completed at:</dt> <dd>@date($task->completed_at)</dd>
                                    @endif
                                </dl>
                            </div>
                            <div class="col-lg-7">
                                <dl class="dl-horizontal m-b-none">
                                    <dt>Category:</dt> <dd>{{ $task->category }}</dd>
                                    <dt>User:</dt> <dd>
                                        @if ($task->user)
                                            {{ $task->user->name }}
                                            @if(! $task->user->active)
                                                (Deactivated)
                                            @endif
                                        @else
                                            <i class="text-muted">no user</i>
                                        @endif
                                    </dd>

                                    <dt>Repeating:</dt>
                                    <dd>
                                        @if ($task->repeating)
                                            @frequency($task->frequency)
                                        @else
                                            <i class="text-muted">not repeating</i>
                                        @endif
                                    </dd>
                                </dl>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        {{-- Description --}}
                        <div class="row">
                            <div class="col-lg-12">
                                @if($task->template)
                                    {!! $task->version->description !!}
                                @else
                                    {!! $task->details->description !!}
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        @if ($userCompletedTask->status == \App\Repositories\UserCompletedTask\UserCompletedTask::STATUS_PENDING)
                            <div class="row">
                                <div class="col-lg-12">
                                    <form method="POST">
                                        {{ csrf_field() }}
                                        <div class="col-lg-6">
                                            <button type="submit" formaction="{{ action('ReviewsController@approveTask', [$review, $userCompletedTask->id]) }}" class="btn btn-xs btn-warning">Approve Task</button>
                                            <label style="margin-left: 30px;" class="issue-tracker issue-info">Approving a task will mark all subtasks as approved (if any)</label>
                                        </div>
                                        <div class="col-lg-6">
                                            <button type="submit" formaction="{{ action('ReviewsController@declineTask', [$review, $userCompletedTask->id]) }}" class="btn btn-xs btn-warning">Decline Task</button>
                                            <label style="margin-left: 30px;" class="issue-tracker issue-info">Declining a task will mark all subtasks as declined (if any)</label>
                                            <br/><br/>
                                            <label>Reason</label>
                                            <textarea name="reason" placeholder="Reason" class="form-control"></textarea>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                        @else
                            <div class="row">
                                <div class="col-lg-12">
                                    <label class="alert {{ $userCompletedTask->status == \App\Repositories\UserCompletedTask\UserCompletedTask::STATUS_APPROVED ? 'alert-success' : 'alert-danger'  }}">{{ strtoupper($userCompletedTask->status) }}</label>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                        @endif

                        {{-- Subtasks --}}
                        @if($task->template_id != NULL)
                            <div class="row">
                                <div class="col-lg-12">
                                    <h3 id="subtasks">Subtasks</h3>

                                    @if ($task->subtasks->count() > 0)
                                        <div class="table-responsive m-t">
                                            <table class="table table-hover issue-tracker">
                                                <tbody>
                                                @foreach ($userCompletedTask->userCompletedSubtasks as $userCompletedSubtask)
                                                    <tr>
                                                        <td>
                                                            @if ($userCompletedSubtask->status == App\Repositories\UserCompletedSubtask\UserCompletedSubtask::STATUS_PENDING)
                                                                <label class="label label-info">{{ strtoupper($userCompletedSubtask->status) }}</label>
                                                            @elseif ($userCompletedSubtask->status == App\Repositories\UserCompletedSubtask\UserCompletedSubtask::STATUS_APPROVED)
                                                                <label class="label label-success">{{ strtoupper($userCompletedSubtask->status) }}</label>
                                                            @else
                                                                <label class="label label-danger">{{ strtoupper($userCompletedSubtask->status) }}</label>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a href="{{ action('ReviewsController@reviewSubtask', [$review->id, $userCompletedTask->id, $userCompletedSubtask->id]) }}">{{ $userCompletedSubtask->subtask->title }}</a>
                                                        </td>
                                                        <td>
                                                            <span class="label label-{{ $task->deadlineClass() }}">
                                                                @if ($userCompletedSubtask->subtask->deadline)
                                                                    @date($userCompletedSubtask->subtask->deadline)
                                                                @elseif ($task->deadline)
                                                                    @date($task->deadline)
                                                                @endif
                                                            </span>
                                                        </td>
                                                        <td class="actions">
                                                            <form method="POST">
                                                                {{ csrf_field() }}
                                                                <div class="row">
                                                                    @if($userCompletedSubtask->status == \App\Repositories\UserCompletedSubtask\UserCompletedSubtask::STATUS_PENDING)
                                                                        <button type="submit" formaction="{{ action('ReviewsController@approveSubtask', [$review, $userCompletedSubtask->id]) }}" class="btn btn-xs btn-primary">Approve Subtask</button>
                                                                        <button type="button" id="decline-subtask-btn" class="btn btn-xs btn-warning">Decline Subtask</button>
                                                                    @endif
                                                                </div>
                                                                @if($userCompletedSubtask->status == \App\Repositories\UserCompletedSubtask\UserCompletedSubtask::STATUS_PENDING)
                                                                <div class="row decline-reason-container" style="display: none">
                                                                    <textarea name="reason" class="form-control" placeholder="Give a reason for declining this subtask."></textarea>
                                                                    <div class="space-15"></div>
                                                                    <button type="submit" formaction="{{ action('ReviewsController@declineSubtask', [$review, $userCompletedSubtask->id]) }}" class="btn btn-xs btn-danger">Decline Subtask</button>
                                                                </div>
                                                                @endif
                                                            </form>
                                                        </td>
                                                        <td class="project-actions">
                                                            <a taget="_blank" href="{{ action('SubtaskController@show', $userCompletedSubtask->subtask) }}" class="btn btn-white btn-sm"><i class="fa fa-folder"></i> View original subtask</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <i class="text-muted">no subtasks</i>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                        @endif

                        {{-- Comments --}}
                        <div class="row">
                            <div class="col-lg-12">
                                @forelse ($task->comments as $comment)
                                    <div class="social-comment">
                                        <div class="media-body">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <strong>{{ $comment->user->name }}</strong>
                                                    @if(! $comment->user->active)
                                                        (Deactivated)
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-10">
                                                    @if ($comment->type === 'reason')
                                                        <strong style="color:#ED5565">Overdue Reason: </strong>
                                                    @endif
                                                    {{ $comment->comment }}<br>
                                                    <small class="text-muted">@datetime($comment->created_at)</small>
                                                    @if ($comment->after_complete)
                                                        <small class="text-muted" style="color: red;"> - Comment was made after task was completed</small>
                                                    @endif
                                                    @if ($comment->from_review_page)
                                                        <small class="text-muted" style="color: red;"> - Comment was made form review page</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <i class="text-muted">no comments</i>
                                @endforelse

                                <div class="social-comment">
                                    <div class="media-body">
                                        <form method="POST" action="{{ action('CommentController@reviewStore', $task) }}">
                                            {{ csrf_field() }}
                                            <textarea name="comment" class="form-control" placeholder="Write comment..."></textarea>
                                            <button type="submit" class="btn btn-white m-t"><i class="fa fa-send"></i> Post</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script type="text/javascript">
        $("#decline-subtask-btn").on('click', function(){
            $(this).parents('.actions').first().find('.decline-reason-container').toggle();
        });
    </script>
@endsection