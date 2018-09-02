@extends('layouts.app')

@section('title', "Group {$group->name}")

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>{{ $group->name }}</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                    @if (auth()->user()->isAdminOrCustomerService())
                        <a href="{{ route('groups.index') }}">Groups</a>
                    @else
                        Groups
                    @endif
                </li>
                <li class="active">
                    <strong>{{ $group->name }}</strong>
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
                                <div class="m-b-md clearfix">
                                    @if (auth()->user()->isAdmin())
                                        <a href="{{ route('groups.users.create', $group) }}" class="btn btn-white btn-xs pull-right">Add user</a>
                                        <form style="margin-right: 5px" class="pull-right" action="{{ route('groups.destroy', $group) }}" method="POST">
                                            {{ csrf_field() }}
                                            {{ method_field('delete') }}
                                            <button class="btn btn-xs btn-danger">Remove group</button>
                                        </form>
                                    @endif
                                    <h2>{{ $group->name }} group</h2>
                                </div>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="m-b-md">
                                    @if (count($group->users))
                                        <table class="table table-hover">
                                            <thead>
                                                <th>#ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th class="text-right">Actions</th>
                                            </thead>
                                            <tbody>

                                                @foreach ($group->users as $user)
                                                    <tr>
                                                        <td>{{ $user->id }}</td>
                                                        <td>{{ $user->name }}
                                                            @if (!$user->active)
                                                                <span class="label label-default">Deactivated</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $user->email }}</td>
                                                        @if (Auth::user()->isAdmin())
                                                            <td>
                                                                <form role="form" method="POST" action="{{ route('groups.users.destroy', [$group, $user]) }}">
                                                                    {{ csrf_field() }}
                                                                    {{ method_field('delete') }}

                                                                    <button class="btn btn-danger btn-outline btn-xs pull-right" type="submit">Remove user</button>
                                                                </form>
                                                            </td>
                                                        @endif
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    @else
                                        <p class="text-muted">No users...</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
