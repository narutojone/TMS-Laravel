@extends('layouts.app')

@section('title', 'Phone Calls Logs')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2>Phone Calls Logs</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                    System Settings
                </li>
                <li class="active">
                    <strong>Phone Calls Logs</strong>
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
                        <h5>Calls</h5>
                    </div>
                    <div class="ibox-content">
                        @if ($logs)
                            <table class="table">
                                <thead>
                                <tr>
                                    <th class="text-center">Call Made</th>
                                    <th class="text-center">From</th>
                                    <th class="text-center">Answered</th>
                                    <th class="text-center">Call Start</th>
                                    <th class="text-center">Call End</th>
                                    <th class="text-center">Call Duration</th>
                                    <th class="text-center">Media Duration</th>
                                    <th class="text-center">Media File</th>
                                    <th class="text-center">Unanswered Call Task</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($logs as $log)
                                    <tr>
                                        <td class="text-center text-muted">
                                            {{ $log->created_at->format('j M, H:i:s') }}
                                        </td>
                                        <td class="text-center text-muted">
                                            @if($log->client)
                                                +{{ $log->from }}
                                                <br/>
                                                <small>
                                                    <a href="{{ action('ClientController@show', ['id' => $log->client_id]) }}">{{ $log->client->name }}</a>
                                                </small>
                                            @else
                                                +{{ $log->from }}
                                                <br/>
                                                <small>
                                                    - not a client -
                                                </small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($log->employee)
                                                @if ($log->media_file)
                                                    <span class="text-warning">+{{ $log->to }}</span>
                                                    <br/>
                                                    <small>
                                                        <a href="{{ action('UserController@show', ['id' => $log->employee_id]) }}">{{ $log->employee->name }}</a>
                                                    </small>
                                                @else
                                                    +{{ $log->to }}
                                                    <br/>
                                                    <small class="text-danger">
                                                        - no answer -
                                                    </small>
                                                @endif
                                            @elseif($log->media_file)
                                                @if ($log->to == $settings['fallback-number'])
                                                    <span class="text-muted">+{{ $settings['fallback-number'] }}</span>
                                                    <br/>
                                                    <small class="text-muted">- fallback number -</small>
                                                @else
                                                    <span class="text-muted">+{{ $log->to }}</span>
                                                    <br/>
                                                    <small class="text-muted">- not an employee -</small>
                                                @endif
                                            @else
                                                <span class="text-muted">+{{ $settings['fallback-number'] }}</span>
                                                <br/>
                                                <small class="text-danger">
                                                    - no answer -
                                                </small>
                                            @endif
                                        </td>
                                        <td class="text-center text-muted">
                                            @if ($log->start_time)
                                                {{ $log->start_time->format('j M, H:i:s') }}
                                            @else
                                                <small>-</small>
                                            @endif
                                        </td>
                                        <td class="text-center text-muted">
                                            @if ($log->end_time)
                                                {{ $log->end_time->format('j M, H:i:s') }}
                                            @else
                                                <small>-</small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($log->call_duration)
                                                {{ gmdate("i:s", $log->call_duration) }}
                                            @else
                                                <small>-</small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($log->media_duration)
                                                {{ gmdate("i:s", $log->media_duration) }}
                                            @else
                                                <small>-</small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($log->media_file)
                                                <a href="{{ $log->media_file }}" target="_blank">
                                                    <i class="fa fa-play-circle text-info action-icons" aria-hidden="true"></i>
                                                </a>
                                            @else
                                                <small>-</small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($task = $log->task)
                                                <a href="{{ route('tasks', $task) }}" target="_blank">
                                                    <i class="fa fa-list text-info action-icons" aria-hidden="true"></i>
                                                    {{ $task->title }}
                                                </a>
                                            @else
                                                <small>-</small>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{ $logs->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection