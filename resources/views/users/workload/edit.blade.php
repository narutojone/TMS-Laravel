@extends('layouts.app')

@section('title', 'Edit ' . $user->name)

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>Edit {{ $user->name }}</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                    <a href="{{ action('UserController@index') }}">Users</a>
                </li>
                <li>
                    <a href="{{ action('UserController@show', $user) }}">{{ $user->name }}</a>
                </li>
                <li class="active">
                    <strong>Edit</strong>
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
                        <h5>Edit user</h5>
                    </div>
                    <div class="ibox-content">
                        <form class="form-horizontal" role="form" method="POST" action="{{ action('UserWorkloadController@update', $user) }}">
                            {{ csrf_field() }}

                            @foreach($userWorkloadMonths as $key => $userWorkloadMonth)
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="name">{{ \Carbon\Carbon::parse($key)->format('Y F') }}</label>

                                    <div class="col-sm-2">
                                        <input type="number" min="0" step="1" class="form-control" name="workload[{{$key}}]" id="name" value="{{ $userWorkloadMonth['hours'] }}" {{ $userWorkloadMonth['locked'] ? 'readonly' : '' }} />

                                        @if ($errors->has('name'))
                                            <span class="help-block m-b-none"><strong>{{ $errors->first('name') }}</strong></span>
                                        @endif
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="checkbox m-r-xs">
                                            <input type="hidden" name="locked[{{$key}}]" value="0" />
                                            <input type="checkbox" name="locked[{{$key}}]" value="1" {{ $userWorkloadMonth['locked'] ? 'checked' : '' }}>
                                            <label class="no-padding">Lock</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                            @endforeach

                            {{-- Submit --}}
                            <div class="form-group m-b-none">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <a class="btn btn-white" href="{{ action('UserController@show', $user) }}">Cancel</a>
                                    <button class="btn btn-primary" type="submit">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
