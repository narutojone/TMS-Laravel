@extends('layouts.app')

@section('title', 'Reports')

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Reports</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url('/dashboard') }}">Dashboard</a>
            </li>
            <li>
                Reports
            </li>
            <li class="active">
                <strong>Overdue Task Report</strong>
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
                        <div class="col-lg-12">
                            <h3 id="tasks">Tasks</h3>
                            <small>(Count: {{ $tasks->count() }})</small>
                            <div class="table-responsive m-t">
                                <table class="table table-hover issue-tracker">
                                    <tbody>
                                        @forelse ($tasks as $task)
                                                <tr>
                                                    <td class="issue-info">
                                                        <a href="{{ action('TaskController@show', $task->task_id) }}">{{ $task->task_title }}</a>
                                                        <small>Overdue reasons: {{ $task->overdue_count }}</small>
                                                    </td>
                                                    <td>
                                                        <a href="{{ action('ClientController@show', $task->client_id) }}">{{ $task->client_name }}</a>
                                                    </td>
                                                    <td>
                                                        @if ($task->user_name)
                                                            <a href="{{ action('UserController@show', $task->user_id) }}">{{ $task->user_name }}</a>
                                                        @else
                                                            <i class="text-muted">no user</i>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="label label-danger">@date($task->task_deadline)</span>
                                                    </td>
                                                    <td class="project-actions">
                                                        <a href="{{ action('TaskController@show', $task->task_id) }}" class="btn btn-white btn-sm">
                                                            <i class="fa fa-folder"></i> View
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <i class="text-muted">no tasks</i>
                                            @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
