@extends('layouts.app')

@section('title', 'Regenerate ' . $task->title)

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <h2>Reopen {{ $task->title }}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url('/dashboard') }}">Dashboard</a>
            </li>
            <li>
                <a href="{{ action('ClientController@index') }}">Clients</a>
            </li>
            <li>
                <a href="{{ action('ClientController@show', $client) }}">{{ $client->name }}</a>
            </li>
            <li>
                <a href="{{ action('ClientController@show', $client) }}">Tasks</a>
            </li>
            <li>
                <a href="{{ action('TaskController@show', $task) }}">{{ $task->title }}</a>
            </li>
            <li class="active">
                <strong>Regenerate</strong>
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
                    <h5>Regenerate {{ $task->title }}</h5>
                </div>
                <div class="ibox-content">
                    <form class="form-horizontal" role="form" method="POST" action="{{ action('TaskController@regenerate', $task) }}">
                        {{ csrf_field() }}

                        {{-- Reason --}}
                        <div class="form-group{{ $errors->has('reason') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="reason">Reason</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="reason" id="reason" value="{{ old('reason') }}" required autofocus>

                                @if ($errors->has('reason'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('reason') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group m-b-none">
                            <div class="col-sm-4 col-sm-offset-2">
                                <a class="btn btn-white" href="{{ action('ClientController@show', $client) }}">Cancel</a>
                                <button class="btn btn-primary" type="submit">Regenerate</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
