@extends('layouts.app')

@section('title', 'Create Custom SMS')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2>Custom SMS</h2>
            <ol class="breadcrumb">
                <li><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('settings.sms.index') }}">Custom SMS</a></li>
                <li class="active"><strong>Create</strong></li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="wrapper wrapper-content animated fadeInUp">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Create a new SMS</h5>
                    </div>
                    <div class="ibox-content">
                        <form id="create-email-template-form" class="form-horizontal" method="POST" action="{{ route('settings.sms.store') }}">
                            {{ csrf_field() }}

                            {{-- Phone Number --}}
                            <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="phone">Phone Number</label>
                                <div class="col-sm-10">
                                    <div class="input-group m-b">
                                        <span class="input-group-addon">+</span>
                                        <input type="number" id="phone" name="phone" placeholder="Phone number" class="form-control" value="{{ old('phone') }}" required autofocus>
                                    </div>

                                    @if ($errors->has('phone'))
                                        <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('phone') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            {{-- Message --}}
                            <div class="form-group{{ $errors->has('message') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="title">Message</label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="message" id="message" value="{{ old('message') }}" required>

                                    @if ($errors->has('title'))
                                        <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('message') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group m-b-none">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <a class="btn btn-white" href="{{ route('settings.sms.index') }}">Cancel</a>
                                    <button class="btn btn-primary" type="submit">Send</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection