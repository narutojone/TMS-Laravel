@extends('layouts.app')

@section('title', $subtask->title)

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>{{ $subtask->title }}</h2>
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
                <li>
                    <a href="{{ action('TaskController@show', $task) }}">{{ $task->title }}</a>
                </li>
                <li>
                    <a href="{{ action('TaskController@show', $task) }}">Subtasks</a>
                </li>
                <li class="active">
                    <strong>{{ $subtask->title }}</strong>
                </li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    @if ($task->reopenings()->count() > 0 && !$subtask->isComplete())
            <div class="col-lg-12">
                <div class="wrapper wrapper-content animated fadeInUp" style="padding-bottom: 0;">
                    <div class="alert alert-warning m-b-none">
                        <strong>This subtask has been re-opened!</strong> Please be careful closing the task before looking at the comments.
                    </div>
                </div>
            </div>
    @endif
    <div class="row">
        <div class="col-lg-12">
            <div class="wrapper wrapper-content">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="m-b-md">
                                    <div class="pull-right">
                                        @if ($subtask->isComplete())
                                            @can('reopen', $subtask)
                                                <a class="btn btn-danger btn-outline btn-xs" href="{{ action('ReopenSubtaskController@form', $subtask) }}">Reopen</a>
                                            @endcan
                                        @else
                                            @can('complete', $subtask)
                                                <form method="post" action="{{ action('SubtaskController@completed', $subtask) }}" style="display: inline-block;">
                                                    {{ csrf_field() }}
                                                    @if($subtask->needsReview())
                                                        <a href="{{action('SubtaskController@reviewChanges', $subtask)}}" class="btn btn-warning btn-xs"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Review changes</a>
                                                    @else
                                                        <button type="submit" class="btn btn-xs btn-white">Mark completed</button>
                                                    @endif
                                                </form>
                                            @endcan
                                        @endif
                                    </div>
                                    <h2>{{ $subtask->title }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-5">
                                <dl class="dl-horizontal m-b-none">
                                    <dt>User:</dt> <dd>
                                        @if ($subtask->user)
                                            {{ $subtask->user->name }}
                                        @elseif ($task->user)
                                            {{ $task->user->name }}
                                        @else
                                            <i class="text-muted">no user</i>
                                        @endif
                                    </dd>
                                    <dt>Deadline:</dt> <dd>
                                        @if ($subtask->deadline)
                                            @date($subtask->deadline)
                                        @elseif ($task->deadline)
                                            @date($task->deadline)
                                        @endif
                                    </dd>
                                </dl>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        {{-- Details --}}
                        @if ($subtask->isComplete())
                            <div class="row">
                                <div class="col-lg-5">
                                    <dl class="dl-horizontal m-b-none">
                                        <dt>Completed at:</dt> <dd>@date($subtask->completed_at)</dd>
                                    </dl>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                        @endif

                        {{-- Description --}}
                        <div class="row">
                            <div class="col-lg-12">
                                {!! $subtask->version->description !!}
                            </div>
                        </div>

                        {{-- Files --}}
                        @if (!$subtask->files->isEmpty())
                            <div class="row">
                                <div class="col-lg-5">
                                    @foreach ($subtask->files as $file)
                                        <dl class="dl-horizontal m-b-none">
                                            <dt>{{$file->created_at}}:</dt>
                                            <dd><a href="{{FileVault::publicUrl($file->filevault_id)}}" target="_blank">File: {{$file->name}}</a></dd>
                                        </dl>
                                    @endforeach
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                        @elseif(!is_null($subtask->upload_not_needed_reason))
                            <div class="row">
                                <div class="col-lg-5">
                                    <label>No file uploaded due to:</label> {{ $subtask->upload_not_needed_reason }}
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                        @endif

                    </div>
                </div>

                {{-- Reopenings --}}
                @if ($subtask->reopenings()->count() > 0)
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Reopenings</h5>
                        </div>
                        <div class="ibox-content">
                            <ul class="list-unstyled">
                                @foreach ($subtask->reopenings()->orderBy('created_at', 'desc')->get() as $reopening)
                                    <li{{ (!$loop->last) ? ' class=m-b' : '' }}>
                                        <strong>
                                            @if ($reopening->reason)
                                                {{ $reopening->reason }}
                                            @else
                                                <i>no reason specified</i>
                                            @endif
                                        </strong>
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
