@extends('layouts.app')

@section('title', 'Clients')

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <h2>Clients</h2>
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
                <strong>Zendesk Tickets</strong>
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
                    <h5>All zendesk tickets assigned to "{{$client->name}}"</h5>
                </div>
                <div class="ibox-content">


                    @if (sizeof($tickets) > 0)
                        <div class="project-list">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Ticket ID</th>
                                    <th>Employee</th>
                                    <th>Status</th>
                                    <th>Title</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tickets as $ticket)
                                        <tr>
                                            <td>#{{$ticket['id']}}</td>
                                            <td>{{isset($asignees[$ticket['assignee']]) ? $asignees[$ticket['assignee']] : 'No assignee'}}</td>
                                            <td>{{$ticket['status']}}</td>
                                            <td>{{$ticket['name']}}</td>
                                            <td><a href="{{route('client.ticket.comments', [$client, $ticket['id']])}}" class="btn btn-white btn-sm"><i class="fa fa-folder"></i> View</a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <i class="text-muted">no tickets</i>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
