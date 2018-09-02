@extends('layouts.app')

@section('title', 'Create client')

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Invite user</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url('/dashboard') }}">Dashboard</a>
            </li>
            <li>
                <a href="{{ url('/users') }}">Users</a>
            </li>
            <li class="active">
                <strong>Invite</strong>
            </li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="wrapper wrapper-content">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Invite user <small>Invite users to the system.</small></h5>
                </div>
                <div class="ibox-content">
                    <form class="form-horizontal" role="form" method="POST" action="{{ action('UserController@store') }}">
                        {{ csrf_field() }}

                        {{-- Name --}}
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="name">Name</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        {{-- E-Mail Address --}}
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="email">E-Mail Address</label>

                            <div class="col-sm-10">
                                <input type="email" class="form-control" name="email" id="email" value="{{ old('email') }}">

                                @if ($errors->has('email'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        {{-- Administrator --}}
                        <div class="form-group{{ $errors->has('admin') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="admin">Administrator</label>

                            <div class="col-sm-10">
                                <input type="checkbox" class="js-switch" id="admin" name="admin">

                                @if ($errors->has('admin'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('admin') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group{{ $errors->has('role') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="category">Role</label>
                            <div class="col-sm-4">
                                <select class="form-control chosen-select" name="role" id="role" style="min-width: 160px;">
                                    <option></option>
                                    @foreach ($roles as $roleId=>$roleName)
                                        <option value="{{$roleId}}">{{ $roleName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group m-b-none">
                            <div class="col-sm-4 col-sm-offset-2">
                                <a class="btn btn-white" href="{{ action('UserController@index') }}">Cancel</a>
                                <button class="btn btn-primary" type="submit">Invite</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
