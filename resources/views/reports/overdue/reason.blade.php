@extends('layouts.app')

@section('title', 'Overdue report user')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>Clients</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                    Reports
                </li>
                <li>
                    <a href="{{ url('/reports/overdue') }}">Overdue Report</a>
                </li>
                <li class="active">
                    <strong>User Report</strong>
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
                    <div class="ibox-title">
                        <h5>{{ $user->name }}{{ $reason ? '. Overdue reason: ' . $reason->reason : '' }}</h5>
                    </div>
                    <div class="ibox-content">
                        @if ($tasks->count() > 0)
                            <div class="project-list">
                                <table class="table table-hover">
                                    <tbody>
                                    @foreach ($tasks as $task)
                                        <tr>
                                            <td class="issue-info">
                                                <a href="{{ action('TaskController@show', $task) }}">{{ $task->title }}</a>
                                                <small>{{ $task->category }}</small>
                                            </td>
                                            <td>
                                                <a href="{{ action('ClientController@show', $task->client) }}">{{ $task->client->name }}</a>
                                            </td>
                                            <td>
                                                @if ($task->user)
                                                    {{ $task->user->name }}
                                                @else
                                                    <i class="text-muted">no user</i>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="label label-danger" style=" background-color: {{$task->overdueReason ? $task->overdueReason->overdueReason->hex : ''}}">@date($task->deadline)</span>
                                            </td>
                                            <td>
                                                <span class="label label-{{ $task->dueDateCountDown()['class'] }}">{{ $task->dueDateCountDown()['label'] }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <div class="text-center">
                                    {{ $tasks->links() }}
                                </div>
                            </div>
                        @else
                            <i class="text-muted">no tasks</i>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection