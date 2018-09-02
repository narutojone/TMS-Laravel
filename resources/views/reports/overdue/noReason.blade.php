@extends('layouts.app')

@section('title', 'Tasks without overdue reason')

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
                    <strong>No Overdue Reason</strong>
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
                        <h5>Tasks without overdue reason</h5>
                    </div>
                    <div class="ibox-content">
                        <form role="form" class="form-inline" method="get" action="">
                            {{-- Search --}}
                            <label class="control-label" for="user">User</label>

                            <select class="form-control chosen-select" name="user" id="user">
                                <option></option>
                                @foreach ($users as $user)
                                    <option{{ ($selectedUser == $user->id) ? ' selected' : '' }} value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>

                            <button class="btn btn-primary m-l-md" type="submit">Filter</button>
                        </form>
                        <div class="hr-line-dashed"></div>
                        @if (count($tasks) > 0)
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
                                                <span class="label label-danger">@date($task->deadline)</span>
                                            </td>
                                            <td>
                                                <span class="label label-{{ $task->dueDateCountDown()['class'] }}">{{ $task->dueDateCountDown()['label'] }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
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