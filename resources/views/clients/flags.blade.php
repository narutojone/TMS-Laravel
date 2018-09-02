@extends('layouts.app')

@section('title', $client->name . ' Flags')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>{{ $client->name }} - Flags</h2>
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
                    <strong>Flags</strong>
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
                            <table class="table table-hover">
                                @if ($flags->count())
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Flag title</th>
                                        <th>Flag date</th>
                                        <th>Valid To</th>
                                        <th>Client</th>
                                        <th>Comment</th>
                                    </tr>
                                </thead>
                                @endif
                                <tbody>
                                @forelse ($flags as $flag)
                                    <tr>
                                        <td>@include('flag-user.flagged', ['color' => $flag->hex])</td>
                                        <td class="project-title">{{ $flag->reason }}</td>
                                        <td>{{ Carbon\Carbon::parse($flag->pivot->created_at)->format('Y-m-d') }}</td>
                                        <td>{{ $flag->validTo() }}</td>
                                        <td>{{ App\Repositories\User\User::find($flag->pivot->user_id)->name }}</td>
                                        <td>{{ $flag->pivot->comment }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <span><em>no flags...</em></span>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                            <div class="text-center">
                                {{ $flags->fragment('flags')->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
