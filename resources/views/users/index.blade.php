@extends('layouts.app')

@section('title', 'Users')

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Users</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url('/dashboard') }}">Dashboard</a>
            </li>
            <li class="active">
                <strong>Users</strong>
            </li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="wrapper wrapper-content">

            {{-- Users --}}
            <div class="ibox">
                <div class="ibox-title">
                    <h5>All users</h5>
                    <div class="ibox-tools">
                        @if(Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                            <a href="{{ action('UserController@create') }}" class="btn btn-primary btn-xs">Create a new user</a>
                        @endif
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="project-list">
                        <table class="table table-hover">
                            <thead>
                                <th>Name</th>
                                <th>Level (0-6)</th>
                                <th>PF ID</th>
                                <th>Authorized</th>
                                <th>User Role</th>
                                <th>Flags</th>
                                <th class="text-right">Actions</th>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td class="project-title">
                                            <a href="{{ action('UserController@show', $user) }}">{{ $user->name }}</a>
                                            @if($user->out_of_office)
                                                <span class="label label-danger m-l-md">Out of office</span>
                                            @endif
                                        </td>
                                        <td>
                                            Level: {{ $user->level }}
                                        </td>
                                        <td>{{$user->pf_id ? 'PF' . $user->pf_id : ''}}</td>
                                        <td>{{$user->authorized ? 'Yes' : 'No'}}</td>
                                        <td class="project-status">
                                            {{-- Display user role --}}
                                            <span class="label label-info">{{\App\Repositories\User\User::$availableRoles[$user->role]}}</span>
                                            @if ($user->id == Auth::user()->id)
                                                <span class="label label-danger">You</span>
                                            @endif
                                        </td>
                                        <td class="project-status">
                                            @if (!$user->active)
                                                <span class="label label-default">Deactivated</span>
                                            @endif
                                            @if ($user->hasFlags())
                                                @include('flag-user.flagged', ['color' => $user->flagColor()])
                                            @endif
                                        </td>
                                        <td class="project-actions">
                                            <a href="{{ action('UserController@show', $user) }}" class="btn btn-white btn-sm"><i class="fa fa-folder"></i> View</a>
                                            @if(Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                                                <a href="{{ action('UserController@edit', $user) }}" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i> Edit</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="text-center">
                            {{ $users->links() }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Invitations --}}
            @if (App\Repositories\Invitation\Invitation::count() > 0)
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Invitations</h5>
                        <div class="ibox-tools">
                            <a href="{{ action('UserController@create') }}" class="btn btn-primary btn-xs">Invite a new user</a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="project-list">
                            <table class="table table-hover">
                                <tbody>
                                    @foreach (App\Repositories\Invitation\Invitation::orderBy('name')->get() as $invitation)
                                        <tr>
                                            <td class="project-title">
                                                {{ $invitation->name }}
                                            </td>
                                            <td>
                                                {{ $invitation->email }}
                                            </td>
                                            <td class="project-actions">
                                                <form method="POST" action="{{ action('InvitationController@destroy', $invitation) }}">
                                                    {{ csrf_field() }}

                                                    <input type="hidden" name="_method" value="DELETE">

                                                    <button type="submit" class="btn btn-white btn-sm"><i class="fa fa-times"></i> Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
