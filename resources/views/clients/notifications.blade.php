@extends('layouts.app')

@section('title', $client->name . ' Notifications')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>{{ $client->name }} - Notifications</h2>
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
                    <strong>Notifications</strong>
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
                            <table class="table">
                                <thead>
                                <tr>
                                    <th class="text-center">To</th>
                                    <th class="text-center">Date sent</th>
                                    <th class="text-center">Notification type</th>
                                    <th class="text-center">Actions/Content</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($notifications as $notification)
                                    <tr>
                                        <td class="text-center text-muted">{{ $notification->to }}</td>
                                        <td class="text-center text-muted">@date($notification->created_at)</td>
                                        <td class="text-center text-muted">{{ $notification->type }}</td>
                                        <td class="text-center text-muted">
                                            <a href="{{ action('NotifierLogController@show', $notification) }}" class="btn btn-white btn-sm">
                                                <i class="fa fa-folder"></i> Show content
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="text-center">
                                {{ $notifications->fragment('notifications')->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
