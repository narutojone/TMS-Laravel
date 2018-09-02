@extends('layouts.app')

@section('title', 'Create user')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2>Create user</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                    <a href="{{ url('/users') }}">Users</a>
                </li>
                <li class="active">
                    <strong>Create</strong>
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
                    <div class="ibox-title"><h5>Create user</h5></div>
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
                                    <input type="email" class="form-control" name="email" id="email" value="{{ old('email') }}" required />

                                    @if ($errors->has('email'))
                                        <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            {{-- Password --}}
                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="password">Password</label>

                                <div class="col-sm-4">
                                    <input type="password" class="form-control" name="password" id="password" value="" required />
                                    @if ($errors->has('password'))
                                        <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <label class="col-sm-2 control-label" for="r-password">Repeat password</label>

                                <div class="col-sm-4">
                                    <input type="password" class="form-control" name="r-password" id="r-password" value="" required />
                                    @if ($errors->has('r-password'))
                                        <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('r-password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            {{-- PF ID --}}
                            <div class="form-group{{ $errors->has('pf_id') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="pf_id">PF ID</label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="pf_id" id="pf_id" value="{{ old('pf_id') }}" />
                                    @if ($errors->has('pf_id'))
                                        <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('pf_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            {{-- Phone Number --}}
                            <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="phone">Phone Number</label>
                                <div class="col-sm-10">
                                    <div class="input-group m-b">
                                        <span class="input-group-addon">+</span>
                                        <input type="number" id="phone" name="phone" placeholder="Phone number" value="{{ old('phone') }}" class="form-control">
                                    </div>

                                    @if ($errors->has('phone'))
                                        <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('phone') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            {{-- Role --}}
                            <div class="form-group{{ $errors->has('role') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="category">Role</label>
                                <div class="col-sm-4">
                                    <select class="form-control chosen-select" name="role" id="role" style="min-width: 160px;">
                                        <option></option>
                                        @foreach ($roles as $roleId=>$roleName)
                                            <option {{ old('role') == $roleId ? 'selected' : '' }} value="{{$roleId}}">{{ $roleName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            {{-- Level --}}
                            <div class="form-group{{ $errors->has('level') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="level">Level</label>
                                <div class="col-sm-4">
                                    <input type="number" class="form-control" name="level" id="level" value="{{ old('level', 0) }}" />
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            {{-- Harvest ID --}}
                            <div class="form-group{{ $errors->has('harvest_id') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="harvest_id">Harvest ID</label>
                                <div class="col-sm-4">
                                    <input type="number" class="form-control" name="harvest_id" id="harvest_id" value="{{ old('harvest_id') }}" />
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group m-b-none">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <a class="btn btn-white" href="{{ action('UserController@index') }}">Cancel</a>
                                    <button class="btn btn-primary" type="submit">Create</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
