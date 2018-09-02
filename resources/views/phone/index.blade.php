@extends('layouts.app')

@section('title', 'Phone System Settings')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2>Phone System</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                    System Settings
                </li>
                <li class="active">
                    <strong>Phone System</strong>
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
                        <h5>Settings</h5>
                        <div class="ibox-tools">
                            <a href="javascript:void(0);" class="btn btn-primary btn-xs">Manage setting groups</a>
                        </div>
                    </div>
                    <div class="ibox-content">

                        @if (session('message'))
                            <div class="alert alert-success">
                                {{ session('message') }}
                            </div>
                        @endif

                        <form method="post" enctype="multipart/form-data" action="{{ route('settings.phone.update') }}" class="form-horizontal">
                            {{ csrf_field() }}
                            @if ($group->settings)
                                @foreach($group->settings as $setting)
                                    <div class="form-group @if($errors->has($setting->setting_key)) has-error @endif">
                                        <label for="{{ $setting->setting_key }}" class="col-sm-2 control-label">{{ $setting->name }}</label>
                                        <div class="col-sm-10">
                                            @if($setting->value && $setting->input_type == 'file' && is_file(Storage::disk('local')->path('public/phone_system/' . $setting->value)))
                                                <p>
                                                    <span class="text-muted">Current file: </span>
                                                    <a href="{{ Storage::url('phone_system/'.$setting->value) }}" target="_blank">{{ $setting->value }}</a>
                                                </p>
                                            @endif
                                            <input id="{{ $setting->setting_key }}" name="{{ $setting->setting_key }}" type="{{ $setting->input_type }}" value="{{ old($setting->setting_key) ? old($setting->setting_key) : $setting->value }}" class="form-control">
                                            <span class="help-block m-b-none">{!! $errors->first($setting->setting_key) !!}</span>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-primary" type="submit">Save changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection