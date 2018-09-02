@extends('layouts.app')

@section('title', 'Create out of office period ')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>Create out of office period for user: {{ $user->name }}</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li class="active">
                    <strong>Create out of office period</strong>
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
                        <h5>Create out of office period</h5>
                    </div>
                    <div class="ibox-content">
                        <form class="form-horizontal" role="form" method="POST" action="{{ action('UserController@showOooTasks', $user) }}">
                            {{ csrf_field() }}

                            {{-- User --}}
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="reason">User</label>

                                <div class="col-sm-10">
                                    <p class="form-control-static">{{ $user->name }} ({{ $user->email }})</p>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            {{-- Reason --}}
                            <div class="form-group{{ $errors->has('reason') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="reason">Reason</label>

                                <div class="col-sm-10">
                                    <select class="form-control chosen-select" name="reason" id="reason">
                                        @foreach ($reasons as $reason)
                                            <option value="{{ $reason->id }}"{{ ($reason->default == \App\Repositories\OooReason\OooReason::DEFAULT) ? ' selected' : '' }}>{{ $reason->name }}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('reason'))
                                        <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('reason') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            {{-- Period (applies also to men) --}}
                            <div class="form-group{{ ($errors->has('from') || $errors->has('to')) ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="from">From date</label>

                                <div class="col-sm-4">
                                    <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" class="form-control" name="from" id="from" value="{{ old('from') }}">
                                    </div>

                                    @if ($errors->has('from'))
                                        <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('from') }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <label class="col-sm-2 control-label" for="to">To date</label>
                                <div class="col-sm-4">
                                    <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" class="form-control" name="to" id="to" value="{{ old('to') }}">
                                    </div>

                                    @if ($errors->has('to'))
                                        <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('to') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>


                            {{-- Submit --}}
                            <div class="form-group m-b-none">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <a class="btn btn-white" href="{{ action('UserController@show', $user) }}">Cancel</a>
                                    <button class="btn btn-primary" type="submit">Save</button>
                                </div>
                            </div>
                        </form>


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
