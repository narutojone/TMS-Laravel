@extends('layouts.app')
@section('title', 'Notification')
@section('head')
    <style>
        .navbar-default{display:none;}
        .navbar{display:none;}
        #page-wrapper{margin:0px;} 
    </style>
@append
@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>
                <div class="pull-right">
                    <a href="{{ action('ClientController@show', $notification->client()->first()) }}/#notifications" class="btn btn-primary btn-lg">Click here to exit preview mode</a>
                </div>
                Notification Preview
            </h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                    Clients
                </li>
                <li>
                    <a href="{{ action('ClientController@show', $notification->client()->first()) }}">{{ $notification->client()->first()->name }}</a>
                </li>
                <li class="active">
                    <strong>Notification Preview</strong>
                </li>
            </ol>
        </div>
    </div>
@endsection
@section('content')
@if($notification->type == 'sms')
    <div class="row">
        <div class="col-lg-12">
            <div class="wrapper wrapper-content">
                <div class="ibox">
                    <div class="ibox-title">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="m-b-md">
                                    <h2>SMS ({{ $notification->to }})</h2>                                
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ibox-content">
                        {{ $body }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
 {!! $body !!}
@endif
@endsection