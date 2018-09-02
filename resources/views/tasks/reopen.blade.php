@extends('layouts.app')

@section('title', 'Reopen ' . $task->title)

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
                <a href="{{ action('ClientController@completed', $client) }}">Completed Tasks</a>
            </li>
            <li>
                <a href="{{ action('TaskController@show', $task) }}">{{ $task->title }}</a>
            </li>
            <li class="active">
                <strong>Reopen</strong>
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
                    <h5>Reopen {{ $task->title }}</h5>
                </div>
                <div class="ibox-content">
                    <form class="form-horizontal" role="form" method="POST" action="{{ action('ReopenController@submit', $task) }}">
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

                        {{-- Subtasks --}}
                        <div class="form-group{{ $errors->has('subtasks') ? ' has-error': '' }}">
                            <label class="col-sm-2 control-label" for="subtasks">Subtasks to reopen</label>

                            <div class="col-sm-10">
                                <div class="row">
                                    @if ($subtasks->count() == 0)
                                        <i class="text-muted">no subtasks</i>
                                    @else
                                        <div class="col-sm-6">
                                            @foreach ($subtasks[0] as $subtask)
                                                <div>
                                                    <label>
                                                        <input type="checkbox" name="subtasks[]" value="{{ $subtask->id }}">
                                                        {{ $subtask->title }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>

                                        @if ($subtasks->count() > 1)
                                            <div class="col-sm-6">
                                                @foreach ($subtasks[1] as $subtask)
                                                    <div>
                                                        <label>
                                                            <input type="checkbox" name="subtasks[]" value="{{ $subtask->id }}">
                                                            {{ $subtask->title }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    @endif
                                </div>

                                @if ($errors->has('subtasks'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('subtasks') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group m-b-none">
                            <div class="col-sm-4 col-sm-offset-2">
                                <a class="btn btn-white" href="{{ action('ClientController@completed', $client) }}">Cancel</a>
                                <button class="btn btn-primary" type="submit">Reopen</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
