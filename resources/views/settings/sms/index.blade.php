@extends('layouts.app')

@section('title', 'SMS Log')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>Custom SMS</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                   Settings
                </li>
                <li class="active">
                    <strong>Custom SMS</strong>
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
                                <a href="{{ action('SMSController@create') }}" class="btn btn-primary btn-xs pull-right">Create SMS</a>
                                <h3>SMS Log</h3>
                            </div>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="project-list">
                            @if (count($logs))
                                <table class="table table-hover">
                                    <thead>
                                        <th class="col-lg-2">Number</th>
                                        <th class="col-lg-3">Message to</th>
                                        <th class="col-lg-4">Content</th>
                                        <th class="col-lg-2">Sent at</th>
                                    </thead>
                                    <tbody>
                                        @foreach ($logs as $log)
                                            <tr>
                                                <td>{{ $log->to }}</td>
                                                @if ($log->client_id)
                                                    <td class="project-title">
                                                        <a href="{{ action('ClientController@show', $log->client) }}">{{ optional($log->client)->name }}</a>
                                                    </td>
                                                @elseif ($log->user_id)
                                                    <td class="project-title">
                                                        <a href="{{ action('UserController@show', $log->user) }}">{{ optional($log->user)->name }}</a>
                                                    </td>
                                                @else
                                                    <td>-</td>
                                                @endif
                                                <td>{{ $log->body }}</td>
                                                <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <div class="text-center">
                                    {{ $logs->links() }}
                                </div>
                            @else
                                <p class="text-muted"><em>No logs ...</em></p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection