@extends('layouts.app')

@section('title', 'Edit ' . $client->name)

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>{{ $client->name }} - Notes</h2>
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
                    <strong>Notes</strong>
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
                            <h3 id="tasks">Notes</h3>
                            <div class="hr-line-dashed"></div>
                            @forelse ($notes as $note)
                                <div class="social-comment">
                                    <div class="media-body">
                                        <strong>{{ $note->user->name }}</strong>
                                        @if(! $note->user->active)
                                                (Deactivated)
                                            @endif
                                        <br>
                                        {{ $note->note }}<br>
                                        <small class="text-muted">@datetime($note->created_at)</small>
                                    </div>
                                </div>
                            @empty
                                <i class="text-muted">no notes</i>
                            @endforelse
                        </div>
                        <div class="text-center">
                            {{ $notes->fragment('notes')->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
