@extends('layouts.app')

@section('title', 'Create reason')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2>User Flagging</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                    <a href="{{ route('settings.flags.index') }}">User Flagging</a>
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
                        <h5>Edit flag `{{ $flag->reason }}`</h5>
                    </div>
                    <div class="ibox-content">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('settings.flags.update', $flag) }}">
                            {{ csrf_field() }}
                            {{ method_field('PATCH') }}

                            <div class="form-group{{ $errors->has('reason') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="reason">Reason</label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="reason" id="reason" value="{{ old('reason', $flag->reason) }}">

                                    @if ($errors->has('reason'))
                                        <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('reason') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Days --}}
                            <div class="form-group{{ $errors->has('days') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="days">Days</label>

                                <div class="col-sm-2">
                                    <input type="number" class="form-control" id="days" name="days" value="{{ old('days', $flag->days) }}">

                                    @if ($errors->has('days'))
                                        <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('days') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Client specific --}}
                            <div class="form-group{{ $errors->has('client_specific') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="client_specific">Client specific</label>

                                <div class="col-sm-10">
                                    <input type="hidden" value="0" name="client_specific" />
                                    <input type="checkbox" class="js-switch" id="client_specific" value="1" name="client_specific" {{old('client_specific', $flag->client_specific) ? 'checked' : ''}} />
                                    <label class="checkbox-inline">ON is client based. OFF is a general flag (no client relation)</label>

                                    @if ($errors->has('client_specific'))
                                        <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('client_specific') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Client removal --}}
                            <div class="form-group{{ $errors->has('client_removal') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="client_removal">Client removal</label>

                                <div class="col-sm-10">
                                    <input type="hidden" value="0" name="client_removal" />
                                    <input type="checkbox" class="js-switch" id="client_removal" value="1" name="client_removal" {{old('client_removal', $flag->client_removal) ? 'checked' : ''}} />
                                    <label class="checkbox-inline">If ON, user will be removed from client. If "Client specific" setting is OFF then remove all clients from user.</label>

                                    @if ($errors->has('client_removal'))
                                        <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('client_removal') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- SMS message body --}}
                            <div class="form-group{{ $errors->has('sms') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="sms">SMS Message</label>

                                <div class="col-sm-10">
                                    <textarea name="sms" id="sms" class="form-control">{{old('sms', $flag->sms)}}</textarea>
                                    <span class="help-block m-b-none">Leave this empty if no SMS needs to be sent to flagged user! You can use <strong>[[clientname]]</strong> variable.</span>

                                    @if ($errors->has('sms'))
                                        <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('sms') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Color --}}
                            <div class="form-group{{ $errors->has('hex') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="hex">Color</label>

                                <div class="col-sm-2">
                                    <input type="text" name="hex", id="hex" class="form-control colorpicker-element" value="{{ old('hex', $flag->hex) }}" />

                                    @if ($errors->has('hex'))
                                        <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('hex') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>

                            <div class="form-group m-b-none">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <a class="btn btn-white" href="{{ route('settings.flags.index') }}">Cancel</a>
                                    <button class="btn btn-primary" type="submit">Save</button>
                                </div>
                            </div>
                        </form>
                        <div class="hr-line-dashed"></div>
                        <form action="{{ route('settings.flags.destroy', $flag) }}" method="POST">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}
                            <button class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection