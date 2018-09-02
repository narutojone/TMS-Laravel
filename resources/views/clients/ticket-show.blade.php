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
            <li>
                <a href="{{ action('ClientController@showTickets', $client) }}">Zendesk Tickets</a>
            </li>
            <li class="active">
                <strong>Ticket #{{ $ticketid }}</strong>
            </li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="wrapper wrapper-content">
            @foreach ($comments as $comment)
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ $asignees[$comment['author']] }} </h5> <i class="text-muted" style="margin-left: 10px;">(Created at: {{ $comment['created_at'] }})</i>
                    </div>
                    <div class="ibox-content" @if(!$comment['public']) style="background-color: lightyellow" @endif>
                        {!! $comment['content'] !!}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
