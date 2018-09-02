@extends('layouts.app')

@section('title', 'Review ' . $task->title)
@if($tasksoverdue)
    @section('head')
        <style>
            .navbar-default{display:none;}
            .navbar{display:none;}
            #page-wrapper{margin:0px;} 
        </style>
    @append
@endif
@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>Review {{ $task->title }}</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                    <a href="{{ action('ClientController@index') }}">Clients</a>
                </li>
                <li>
                    <a href="{{ action('ClientController@show', $task->client) }}">{{ $task->client->name }}</a>
                </li>
                <li>
                    <a href="{{ action('ClientController@completed', $task->client) }}">Tasks</a>
                </li>
                <li>
                    <a href="{{ action('TaskController@show', $task) }}">{{ $task->title }}</a>
                </li>
                <li class="active">
                    <strong>Review</strong>
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
                        <h5>Review {{ $task->title }}</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-md-2"></div>
                            @if($noChanges)
                                <div class="col-md-10">Task details</div>
                            @else
                                <div class="col-md-5 font-bold">Before changes</div>
                                <div class="col-md-5 font-bold">After changes</div>
                            @endif
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="row">
                            <div class="col-md-2 text-right font-bold {{($oldVersionDetails['title'] != $currentVersionDetails['title']) ? 'bg-danger' : ''}}">Title</div>
                            @if($noChanges)
                                <div class="col-md-10">{{ $currentVersionDetails['title'] }}</div>
                            @else
                                <div class="col-md-5">{{ $oldVersionDetails['title'] }}</div>
                                <div class="col-md-5">{{ $currentVersionDetails['title'] }}</div>
                            @endif
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="row">
                            <div class="col-md-2 text-right font-bold {{($currentVersionDetails['description'] != $oldVersionDetails['description']) ? 'bg-danger' : ''}}">Description</div>
                            @if($noChanges)
                                <div class="col-md-5 text-center">
                                    {!! $currentVersionDetails['description'] !!}
                                </div>
                            @else
                                <div class="col-md-5 text-center">
                                    {!! $oldVersionDetails['description'] !!}
                                </div>
                                <div class="col-md-5 text-center">
                                    {!! $currentVersionDetails['description'] !!}
                                </div>
                            @endif
                        </div>

                        <div class="hr-line-dashed"></div>

                        <form class="form-horizontal" role="form" method="POST" action="{{ action('TaskController@acceptReviewedChanges', $task) }}">
                            {{ csrf_field() }}
                            <div class="form-group m-b-none">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-primary" type="submit">{{($noChanges ? 'Accept task details' : 'Accept changes')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
