@extends('layouts.app')

@section('title', 'Complete ' . $subtask->title)
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
        <h2>Complete {{ $subtask->title }}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url('/dashboard') }}">Dashboard</a>
            </li>
            <li>
                <a href="{{ action('ClientController@index') }}">Clients</a>
            </li>
            <li>
                <a href="{{ action('ClientController@show', $subtask->task->client) }}">{{ $subtask->task->client->name }}</a>
            </li>
            <li>
                <a href="{{ action('ClientController@completed', $subtask->task->client) }}">Tasks</a>
            </li>
            <li>
                <a href="{{ action('TaskController@show', $subtask->task) }}">{{ $subtask->task->title }}</a>
            </li>
            <li>
                <a href="{{ action('TaskController@show', $subtask->task) }}">Subtasks</a>
            </li>
            <li>
                <a href="{{ action('SubtaskController@show', $subtask) }}">{{ $subtask->title }}</a>
            </li>
            <li class="active">
                <strong>Complete subtask</strong>
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
                <div class="ibox-title"><h5>{{ $subtask->title }}</h5>
                    <div class="row">
                        <div class="col-lg-12">
                            <strong>User:</strong>
                            @if ($subtask->user)
                                {{ $subtask->user->name }}
                            @elseif ($subtask->task->user)
                                {{ $subtask->task->user->name }}
                            @else
                                <i class="text-muted">no user</i>
                            @endif
                            <br>
                            <strong>Deadline:</strong>
                            @if ($subtask->deadline)
                                @date($subtask->deadline)
                            @elseif ($subtask->task->deadline)
                                @date($subtask->task->deadline)
                            @endif
                        </div> 
                    </div>
                    <div class="hr-line-dashed"></div>
                    {{-- Description --}}
                    <div class="row">
                        <div class="col-lg-12">
                            {!! $subtask->version->description !!}
                        </div>
                    </div>
                </div>
            </div>


            <form id="form" enctype="multipart/form-data" class="form-horizontal" role="form" method="POST" action="{{ action('SubtaskController@completed', $subtask) }}">
                {{ csrf_field() }}

                {{-- Output a sepparate section for each module --}}
                @foreach($modules as $module)
                    @if(in_array($module->id, $activeModules) && $module->user_input)
                        <div class="ibox float-e-margins">
                            <div class="ibox-title"><h5>{{ isset($settings[$module->id]['custom-title']) && !empty($settings[$module->id]['custom-title']) ? $settings[$module->id]['custom-title'] : $module->name}}</h5></div>
                            <div class="ibox-content">
                                @include("templates.subtasks.modules.$module->template.complete", ['settings'=>$settings[$module->id]])
                            </div>
                        </div>
                    @endif
                @endforeach

                {{-- Form controls --}}
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <div class="desc">
                            <div class="form-group m-b-none">
                                <div class="col-sm-4">
                                    <a class="btn btn-white" href="{{ action('TaskController@show', $subtask->task) }}">Cancel</a>
                                    <button class="btn btn-primary" type="submit">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
