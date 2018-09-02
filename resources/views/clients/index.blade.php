@extends('layouts.app')

@section('title', 'Clients')

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Clients</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url('/dashboard') }}">Dashboard</a>
            </li>
            <li class="active">
                <strong>Clients</strong>
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
                    <h5>All clients assigned to this account</h5>
                    @can('create', App\Repositories\Client\Client::class)
                        <div class="ibox-tools">
                            <a href="{{ action('ClientController@create') }}" class="btn btn-primary btn-xs">Create new client</a>
                        </div>
                    @endcan
                </div>
                <div class="ibox-content">
                    <form role="form" class="form-inline" method="get" action="">
                        {{-- Search --}}
                        <label class="control-label m-r-xs" for="search">Search</label>

                        <input class="form-control" type="text" placeholder="Name or org. nr" name="search" id="search" value="{{ $currentSearch }}" autofocus>

                        @if(! Request::is('clients/internal'))
                            <label class="control-label" for="order" style="margin:0 20px;">Order by</label>

                            <select name="order" class="form-control" >
                                @foreach($orderOptions as $ordKey=>$ordValue)
                                    <option value="{{$ordKey}}" {{($ordKey == app('request')->input('order',1 )) ? 'selected="selected"' : ''}}>{{$ordValue}}</option>
                                @endforeach
                            </select>
                            @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                                <label class="control-label" for="order" style="margin:0 20px;">Manager</label>

                                <select name="manager" class="form-control" >
                                    <option value="" {{( empty(app('request')->input('manager'))) ? 'selected="selected"' : ''}}>All managers</option>
                                    @foreach($managers as $managerId => $managerName)
                                        <option value="{{$managerId}}" {{($managerId == app('request')->input('manager')) ? 'selected="selected"' : ''}}>{{$managerName}}</option>
                                    @endforeach
                                </select>
                            @endif
                        @endif

                        <button class="btn btn-primary m-l-md" type="submit">Filter</button>
                    </form>
                    <div class="hr-line-dashed"></div>

                    @if ($clients->count() > 0)
                        <div class="project-list">
                            <table class="table table-hover">
                                <tbody>
                                    @foreach ($clients as $client)
                                        <tr>
                                            <td class="project-title">
                                                <a href="{{ action('ClientController@show', $client->id) }}">{{ $client->name }}</a>
                                                <span style='margin-left:20px;' class="label label-danger">{{$client->risk ? 'High risk' : ''}}</span>
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
                                                @if($client->internal)
                                                    <span class="label m-l-md">Internal Project</span>
                                                @endif

                                                @if ($client->manager_id == Auth::user()->id)
                                                    <span class="label label-info">Manager</span>
                                                @endif
                                                {{-- @if($client->system)
                                                    <br>
                                                    <small>{{ $client->system->name }}</small>
                                                @endif --}}
                                            </td>
                                            <td class="project-actions">
                                                <a href="{{ action('ClientController@show', $client->id) }}" class="btn btn-white btn-sm">
                                                    <i class="fa fa-folder"></i> View
                                                </a>
                                                @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN) || Auth::user()->hasRole(\App\Repositories\User\User::ROLE_CUSTOMER_SERVICE) || Auth::user()->id == $client->manager_id)
                                                    <a href="{{ action('ClientController@edit', $client->id) }}" class="btn btn-white btn-sm">
                                                        <i class="fa fa-pencil"></i> Edit
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="text-center">
                                {{ $clients->links() }}
                            </div>
                        </div>
                    @else
                        <i class="text-muted">no clients</i>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
