@extends('layouts.app')

@section('title', $task->title)
@section('head')
    <style>
        .navbar-default{display:none;}
        .navbar{display:none;}
        #page-wrapper{margin:0px;} 

        .descriptiontext {
          display: none;
        }
    </style>
@append
@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>Please add an overdue reason</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                    <a href="{{ action('ClientController@index') }}">Clients</a>
                </li>
                <li>
                    <a href="{{ action('ClientController@show', $task->client) }}">{{ $task->client->name }}</a>
                </li>
                @if ($task->isComplete())
                    <li>
                        <a href="{{ action('ClientController@completed', $task->client) }}">Completed Tasks</a>
                    </li>
                @else
                    <li>
                        <a href="{{ action('ClientController@show', $task->client) }}">Tasks</a>
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
        <div class="col-lg-12">
            <div class="wrapper wrapper-content animated fadeInUp" style="padding-bottom: 0;">
                <div class="alert alert-warning m-b-none">
                    <strong>This task is missing an overdue reason!</strong> Please add a reason or complete the task to continue.
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="wrapper wrapper-content">
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
                                                    <button type="submit" class="btn btn-xs btn-primary" {{$task->client->active ? '' : 'disabled' }}><i class="fa fa-check" aria-hidden="true"></i> Mark completed</button>
                                                @endif
                                            </form>
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

                        {{-- Overdue Reason --}}
                        <div class="row">
                            <div class="col-lg-{{ $task->template_id != NULL ? 6 : 12 }}">
                                <h3>Give the task an overdue reason</h3>
                                <form role="form" method="POST" action="{{ action('TaskController@createOverdue', $task) }}">
                                    {{ csrf_field() }}
                                    <div class="form-group"><label>Reason</label>
                                        <select id="reason" class="form-control chosen-select" name="reason" required>
                                            <option disabled selected>Select a reason</option>
                                            @foreach ($overdues as $overdue)
                                                <option value="{{ $overdue->id }}">{{ $overdue->reason }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="wrapper wrapper-content" style="padding-left: 0px; padding-right: 0px;">
                                        <label>Description</label>
                                        <div class="alert alert-info m-b-none">
                                            @foreach ($overdues as $overdue)
                                                <div id="{{ $overdue->id }}" class="{{ $overdue->id }} descriptiontext">
                                                    @if(!empty($overdue->description))
                                                        {!! $overdue->description !!}
                                                    @else
                                                        <small>No description</small>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Zendesk Ticket ID</label> (<a href="https://synega.onelogin.com/launch/617623" target="_blank">Trykk her for å åpne Zendesk</a>)
                                        <input type="text" name="ticket_id" placeholder="Zendesk Ticket ID" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Comment</label>
                                        <textarea name="comment" placeholder="Comment" class="form-control"></textarea>
                                    </div>
                                    <div>
                                        <input type="hidden" name="overdue" value="1">
                                        <button class="btn btn-primary" type="submit">Create reason</button>
                                    </div>
                                </form>
                            </div>
                            @if($task->template_id != NULL)
                                <div class="col-lg-6">
                                    <h3 id="subtasks">Or complete the subtasks</h3>
                                    @if($subtasks->count() > 0)
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
                                                                            
                                                                            <input type="hidden" name="overdue" value="1">

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
                            @endif
                        </div>
                        <div class="hr-line-dashed"></div>

                        {{-- Comments --}}
                        <div class="row">
                            <div class="col-lg-12">
                                <h3>Comments &amp; Overdue Reasons</h3>
                                @forelse ($commentsAndReasons as $comment)
                                    <div class="social-comment">
                                        <div class="media-body">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    @if($comment['user'])
                                                        <strong>{{ $comment['user']->name }}</strong>
                                                        @if(! $comment['user']->active)
                                                            (Deactivated)
                                                        @endif
                                                    @else
                                                        <strong>System</strong>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-10">
                                                    @if ($comment['type'] === 'reason')
                                                        <strong style="color:#ED5565">Overdue Reason: </strong>
                                                    @endif
                                                    {{ $comment['comment'] }}<br>
                                                    <small class="text-muted">@datetime($comment['created_at'])</small>
                                                    @if ($comment['after_complete'])
                                                        <small class="text-muted" style="color: red;"> - Comment was made after task was completed</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <i class="text-muted">no comments</i>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Reopenings --}}
                @if ($task->reopenings()->count() > 0)
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Reopenings</h5>
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
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        $(function() {
          $('#reason').change(function(){
            $('.descriptiontext').hide();
            $('#' + $(this).val()).show();
          });
        });
    </script>
@append