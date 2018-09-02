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
                <strong>Overdue Client Report</strong>
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
                            <h3 id="clients">Clients</h3>
                            <small>(Count: {{ $clients->count() }} clients)</small>
                            <div class="table-responsive m-t">
                                <table class="table table-hover issue-tracker">
                                    <thead>
                                        <tr>
                                            <td>
                                                <strong>Client name</strong>
                                            </td>
                                            <td>
                                                <strong>Employee name</strong>
                                            </td>
                                            <td>
                                                <strong>Total overdue tasks</strong>
                                            </td>
                                            <td>
                                                <strong>Total overdue reasons</strong>
                                            </td>
                                            <td>
                                                <strong>Actions</strong>
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($clients as $client)
                                                <tr>
                                                    <td class="issue-info">
                                                        <a href="{{ action('ClientController@show', $client->client_id) }}">{{ $client->client_name }}</a>
                                                        @if(! $client->paid)
                                                            <span class="label label-danger m-l-md">Not paid</span>
                                                        @endif
                                                        @if(! $client->active)
                                                            <span class="label label-danger m-l-md">Not active</span>
                                                        @endif
                                                        @if($client->paused)
                                                            <span class="label label-danger m-l-md">Paused</span>
                                                        @endif
                                                        @if($client->complaint_case)
                                                            <span class="label label-warning m-l-md">Active Complaint Case</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($client->user_name)
                                                            <a href="{{ action('UserController@show', $client->user_id) }}">{{ $client->user_name }}</a>
                                                        @else
                                                            <i class="text-muted">no user</i>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <strong>{{ $client->task_count }}</strong> tasks
                                                    </td>
                                                    <td>
                                                        <strong>{{ $client->overdue_count }}</strong> reasons
                                                    </td>
                                                    <td class="project-actions">
                                                        <a href="{{ action('ClientController@show', $client->client_id) }}" class="btn btn-white btn-sm">
                                                            <i class="fa fa-folder"></i> View client
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <i class="text-muted">no clients</i>
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
