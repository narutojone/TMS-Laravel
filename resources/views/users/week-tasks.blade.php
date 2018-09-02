@extends('layouts.app')

@section('title', $user->name)

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>
                {{ $user->name }}
            </h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                    <a href="{{ action('UserController@index') }}">Users</a>
                </li>
                <li class="">
                    <a href="{{ action('UserController@show', $user) }}">{{ $user->name }}</a>
                </li>
                <li class="active">
                    <strong>{{ $breadcrumbItem }}</strong>
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
                                <div class="pull-right">
                                    @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                                        <div class="btn-group align-right">
                                            <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle">Add task <span class="caret"></span></button>
                                            <ul class="dropdown-menu">
                                                <li><a href="{{ action('TaskController@create', $user->client_id) }}">From template</a></li>
                                                <li><a href="{{ action('TaskController@createCustom', $user->client_id) }}">Custom</a></li>
                                            </ul>
                                        </div>
                                    @else
                                        <div class="btn-group align-right">
                                            <a href="{{ action('TaskController@createCustom', $user->client_id) }}" class="btn btn-primary btn-xs">Add task</a>
                                        </div>
                                    @endif
                                </div>
                                <h3 id="tasks">Tasks</h3>

                                <div class="table-responsive m-t">
                                    <table class="table table-hover issue-tracker">
                                        <tbody>
                                        @forelse ($tasks as $task)
                                            @if ( ! Auth::user()->admin && ($task->client->manager_id != Auth::user()->id) && $task->taskOverdueReasons()->orderBy('created_at', 'DESC')->first() && ! $task->taskOverdueReasons()->orderBy('created_at', 'DESC')->first()->overdueReason->visible)
                                                @continue
                                            @endif
                                            <tr>
                                                <td class="issue-info">
                                                    <a href="{{ action('TaskController@show', $task) }}">{{ $task->title }}</a>
                                                    <small>{{ $task->category }}</small>
                                                </td>
                                                <td>
                                                    @if ($task->user)
                                                        @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN) || Auth::user()->hasRole(\App\Repositories\User\User::ROLE_LIGHT_ADMIN))
                                                            <a href="{{ action('UserController@show', $task->user) }}">{{ $task->user->name }}</a>
                                                        @else
                                                            {{ $task->user->name }}
                                                        @endif
                                                    @else
                                                        <i class="text-muted">no user</i>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="label label-{{ $task->deadlineClass() }}" style="{{ $task->isOverdue() && $task->overdueReason ? 'background-color:' . $task->overdueReason->overdueReason->hex : '' }}">@date($task->deadline)</span>
                                                </td>
                                                <td>
                                                    <span class="label label-{{ $task->dueDateCountDown()['class'] }}">{{ $task->dueDateCountDown()['label'] }}</span>
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