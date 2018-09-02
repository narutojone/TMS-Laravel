@extends('layouts.app')

@section('title', $task->title)
@if($tasksoverdue)
    @section('head')
        <style>
            .navbar-default{display:none;}
            .navbar{display:none;}
            #page-wrapper{margin:0px;} 
        </style>
    @append
@endif
@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>{{ $task->title }}</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                    <a href="{{ action('ClientController@index') }}">Clients</a>
                </li>
                <li>
                    <a href="{{ action('ClientController@show', $client) }}">{{ $client->name }}</a>
                </li>
                @if ($task->isComplete())
                    <li>
                        <a href="{{ action('ClientController@completed', $client) }}">Completed Tasks</a>
                    </li>
                @else
                    <li>
                        <a href="{{ action('ClientController@show', $client) }}">Tasks</a>
                    </li>
                @endif
                <li class="active">
                    <strong>{{ $task->title }}</strong>
                </li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">

        @if($declinedReview)
            <div class="col-lg-12">
                <div class="wrapper wrapper-content animated fadeInUp" style="padding-bottom: 0;">
                    <div class="alert alert-danger m-b-none">
                        <strong>Review declined!</strong> This task has been reopened by it's reviewer
                    </div>
                </div>
            </div>
        @endif

        @if(!$task->client->paid)
            <div class="col-lg-12">
                <div class="wrapper wrapper-content animated fadeInUp" style="padding-bottom: 0;">
                    <div class="alert alert-danger m-b-none">
                        <strong>Client is deactived!</strong> Invoice has not been paid.
                    </div>
                </div>
            </div>
        @endif

        @if($task->client->complaint_case)
            <div class="col-lg-12">
                <div class="wrapper wrapper-content animated fadeInUp" style="padding-bottom: 0;">
                    <div class="alert alert-warning m-b-none">
                        <strong>This customer has an active complaint case!</strong> Please be careful regarding work on this customer.
                    </div>
                </div>
            </div>
        @endif

        @if ($task->reopenings()->count() > 0 && !$task->isComplete())
            <div class="col-lg-12">
                <div class="wrapper wrapper-content animated fadeInUp" style="padding-bottom: 0;">
                    <div class="alert alert-warning m-b-none">
                        <strong>This task has been re-opened!</strong> Please be careful closing the task before looking at the comments.
                    </div>
                </div>
            </div>
        @endif

        @if($task->delivered)
            <div class="col-lg-12">
                <div class="wrapper wrapper-content animated fadeInUp" style="padding-bottom: 0;">
                    <div class="alert alert-success m-b-none">
                        <strong>The customer has delivered!</strong> The task should be ready to be completed as the client has delivered everything needed to complete the task.
                    </div>
                </div>
            </div>
        @endif

        <div class="col-lg-12">
            <div class="wrapper wrapper-content">
                {{-- Reopenings --}}
                @if ($task->reopenings()->count() > 0)
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>This task has been re-opened!</h5>
                        </div>
                        <div class="ibox-content">
                            <ul class="list-unstyled">
                                @foreach ($task->reopenings()->orderBy('created_at', 'desc')->get() as $reopening)
                                    <li{{ (!$loop->last) ? ' class=m-b' : '' }}>
                                        <strong>{{ $reopening->reason }}</strong>
                                        <br>
                                        Old completion date: @datetime($reopening->completed_at)
                                        <br>
                                        <small class="text-muted">@datetime($reopening->created_at)</small>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="m-b-md">
                                    <div class="pull-right">
                                        @can('complete', $task)
                                            <form method="post" action="{{ action('TaskController@completed', $task) }}" style="display: inline-block;">
                                                {{ csrf_field() }}
                                                @if($task->needsReview())
                                                    <a href="{{action('TaskController@reviewChanges', $task)}}" class="btn btn-white btn-xs"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Review changes</a>
                                                @else
                                                    <button type="submit" class="btn btn-xs btn-primary"><i class="fa fa-check" aria-hidden="true"></i> Mark completed</button>
                                                @endif
                                            </form>
                                        @endcan

                                        @if ($task->askForOverdueReason() && $task->client->active)
                                            <a data-toggle="modal" class="btn btn-white btn-xs" href="#modal-form-{{ $task->id }}">Update overdue reason</a>
                                            @include('tasks.add-overdue-reason', ['tasks' => $task, 'overdues' => $overdues])
                                        @endif
                                        @can('regenerate', $task)
                                            <a href="{{ action('TaskController@showRegenerateForm', $task) }}" style='' class="btn btn-xs btn-warning"><i class="fa fa-refresh" aria-hidden="true"></i> Regenerate</a>
                                        @endcan

                                        @can('update', $task)
                                            <a href="{{ action('TaskController@edit', $task) }}" class="btn btn-white btn-xs">Edit</a>
                                        @endcan
                                    </div>
                                    <h2>{{ $task->title }}</h2>
                                </div>
                            </div>
                        </div>

                        {{-- Details --}}
                        <div class="row">
                            <div class="col-lg-5">
                                <dl class="dl-horizontal m-b-none">
                                    <dt>Category:</dt> <dd>{{ $task->category }}</dd>
                                    <dt>Deadline:</dt> <dd>@date($task->deadline)</dd>

                                    @if ($task->isComplete())
                                        <dt>Completed at:</dt> <dd>@date($task->completed_at)</dd>
                                    @endif
                                </dl>
                            </div>
                            <div class="col-lg-7">
                                <dl class="dl-horizontal m-b-none">
                                    <dt>User:</dt> <dd>
                                        @if ($task->user)
                                            @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN) || Auth::user()->hasRole(\App\Repositories\User\User::ROLE_CUSTOMER_SERVICE))
                                                <a href="{{ action('UserController@show', $task->user) }}">{{ $task->user->name }}</a>
                                            @else
                                                {{ $task->user->name }}
                                            @endif
                                            @if(! $task->user->active)
                                                (Deactivated)
                                            @elseif($task->user->out_of_office)
                                                <span class="label label-danger m-l-md">Out of office</span>
                                            @endif
                                        @else
                                            <i class="text-muted">no user</i>
                                        @endif
                                    </dd>

                                    <dt>Repeating:</dt>
                                    <dd>
                                        @if ($task->repeating)
                                            @frequency($task->frequency)
                                            @if($task->end_date)
                                                <small class="text-muted">(Ends on @date($task->end_date))</small>
                                            @endif
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
                                @if($task->isCustom())
                                    {!! $task->details->description !!}
                                @else
                                    {!! $task->version->description !!}
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        {{-- Subtasks --}}
                        @if($task->template_id != NULL)
                        <div class="row">
                            <div class="col-lg-12">
                                <h3 id="subtasks">Subtasks</h3>

                                @if ($subtasks->count() > 0)
                                    <div class="table-responsive m-t">
                                        <table class="table table-hover issue-tracker">
                                            <tbody>
                                                @foreach ($subtasks as $subtask)
                                                    <tr>
                                                        <td>
                                                            @if ($subtask->isComplete())
                                                                @can('reopen', $subtask)
                                                                    <a class="btn btn-xs btn-white" href="{{ action('ReopenSubtaskController@form', $subtask) }}">Reopen</a>
                                                                @endcan
                                                            @else
                                                                @can('complete', $subtask)
                                                                    <form method="post" action="{{ action('SubtaskController@completed', $subtask) }}">
                                                                        {{ csrf_field() }}
                                                                        @if($subtask->needsReview())
                                                                            <a href="{{action('SubtaskController@reviewChanges', $subtask)}}" class="btn btn-warning btn-xs"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Review changes</a>
                                                                        @else
                                                                            <button type="submit" class="btn btn-xs btn-white">Mark completed</button>
                                                                        @endif
                                                                    </form>
                                                                @endcan
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($subtask->isComplete())
                                                                <s><a href="{{ action('SubtaskController@show', $subtask) }}">{{ $subtask->title }}</a></s>
                                                            @elseif ($subtask->needsReview())
                                                                {{ $subtask->title }}
                                                            @else
                                                                <a href="{{ action('SubtaskController@show', $subtask) }}">{{ $subtask->title }}</a>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($subtask->user)
                                                                @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                                                                    <a href="{{ action('UserController@show', $subtask->user) }}">{{ $subtask->user->name }}</a>
                                                                @else
                                                                    {{ $subtask->user->name }}
                                                                @endif
                                                                @if(! $subtask->user->active)
                                                                    (Deactivated)
                                                                @elseif($subtask->user->out_of_office)
                                                                    <span class="label label-danger m-l-md">Out of office</span>
                                                                @endif
                                                            @elseif ($task->user)
                                                                @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                                                                        <a href="{{ action('UserController@show', $task->user) }}">{{ $task->user->name }}</a>
                                                                @else
                                                                    {{ $task->user->name }}
                                                                @endif
                                                                @if(! $task->user->active)
                                                                    (Deactivated)
                                                                @endif
                                                            @else
                                                                <i class="text-muted">no user</i>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="label label-{{ $task->deadlineClass() }}">
                                                                @if ($subtask->deadline)
                                                                    @date($subtask->deadline)
                                                                @elseif ($task->deadline)
                                                                    @date($task->deadline)
                                                                @endif
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="label label-{{ $task->dueDateCountDown()['class'] }}">{{ $task->dueDateCountDown()['label'] }}</span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                        <div class="text-center">
                                            {{ $subtasks->fragment('subtasks')->links() }}
                                        </div>
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
                                @forelse ($comments as $comment)
                                    <div class="social-comment">
                                        <div class="media-body">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    @if($comment->user)
                                                        <strong>{{ $comment->user->name }}</strong>
                                                        @if(! $comment->user->active)
                                                            (Deactivated)
                                                        @endif
                                                    @else
                                                        <strong>System</strong>
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
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <i class="text-muted">no comments</i>
                                @endforelse

                                <div class="social-comment">
                                    <div class="media-body">
                                        <form method="POST" action="{{ action('CommentController@store', $task) }}">
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

@section('script')
    <script type="text/javascript">
        $( document ).ready(function() {
            $('.modal-dialog button[type="submit"]').click(function(){
                $(this).hide();
            });
        });
    </script>
@endsection
