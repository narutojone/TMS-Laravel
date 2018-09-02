@extends('layouts.app')

@section('title', $client->name)

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>{{ $client->name }}</h2>
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
                <li class="active">
                    <strong>Completed tasks</strong>
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
                                <h2>Completed tasks</h2>

                                <div class="table-responsive m-t">
                                    <table class="table table-hover issue-tracker">
                                        <tbody>
                                            @forelse ($tasks as $task)
                                                <tr>
                                                    <td>
                                                        @if($client->active)
                                                            <a href="{{ action('ReopenController@form', $task) }}" class="btn btn-xs btn-white">Reopen</a>
                                                        @else
                                                            <button type="button" class="btn btn-xs btn-white" disabled>Reopen</button>
                                                        @endif
                                                    </td>
                                                    <td class="issue-info">
                                                        <a href="{{ action('TaskController@show', $task) }}">{{ $task->title }}</a>
                                                        <small>{{ $task->category }}</small>
                                                    </td>
                                                    <td>
                                                        {{ $task->user->name }}
                                                    </td>
                                                    <td>
                                                        <span class="label label-{{ $task->deadlineClass() }}">@date($task->deadline)</span>
                                                    </td>
                                                    <td>
                                                        @date($task->completed_at)
                                                    </td>
                                                    <td>
                                                        @if ($task->repeating)
                                                            {{ $task->frequency }}
                                                        @else
                                                            <i class="text-muted">not repeating</i>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <i class="text-muted">no completed tasks</i>
                                            @endforelse
                                        </tbody>
                                    </table>

                                    <div class="text-center">
                                        {{ $tasks->links() }}
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
