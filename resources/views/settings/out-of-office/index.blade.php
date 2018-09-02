@extends('layouts.app')

@section('title', 'Out of office reasons')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2>Out of office reasons</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li class="active">
                    <strong>Out of office reasons</strong>
                </li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="wrapper wrapper-content">

                {{-- Out of office reasons --}}
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>All reasons</h5>
                        <div class="ibox-tools">
                            @if(Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                                <a href="{{ action('OooController@create') }}" class="btn btn-primary btn-xs">Create a new reason</a>
                            @endif
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="project-list">
                            <table class="table table-hover">
                                <thead>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th class="text-right">Actions</th>
                                </thead>
                                <tbody>
                                @foreach ($reasons as $reason)
                                    <tr>
                                        <td class="project-title">
                                            {{ $reason->name }}
                                        </td>
                                        <td class="">
                                            @if ($reason->default == \App\Repositories\OooReason\OooReason::DEFAULT)
                                                <span class="label label-info">Default</span>
                                            @endif
                                        </td>
                                        <td class="project-actions">
                                            @if(Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                                                <a href="{{ action('OooController@edit', $reason) }}" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i> Edit</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
