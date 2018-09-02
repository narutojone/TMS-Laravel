@extends('layouts.app')

@section('title', 'Reopen ' . $subtask->title)

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <h2>Reopen {{ $subtask->title }}</h2>
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
                <a href="{{ action('ClientController@completed', $client) }}">Tasks</a>
            </li>
            <li>
                <a href="{{ action('TaskController@show', $task) }}">{{ $task->title }}</a>
            </li>
            <li>
                <a href="{{ action('TaskController@show', $task) }}">Subtasks</li>
            </li>
            <li>
                <a href="{{ action('SubtaskController@show', $subtask) }}">{{ $subtask->title }}</a>
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
                    <h5>Reopen {{ $subtask->title }}</h5>
                </div>
                <div class="ibox-content">
                    <form class="form-horizontal" role="form" method="POST" action="{{ action('ReopenSubtaskController@submit', $subtask) }}">
                        {{ csrf_field() }}

                        {{-- User --}}
                        <div class="form-group{{ $errors->has('user') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="user">User</label>

                            <div class="col-sm-10" id="users-select-wrapper">
                                <select class="form-control chosen-select" name="user" id="user">
                                    <option v-if="! users.length">No users available</option>
                                    <option v-else>Select user...</option>
                                    <option :selected="user.id == {{ old('user', $subtask->user_id ?? 0) }}" :value="user.id" v-show="users.length" v-for="user in users" v-text="user.name"></option>
                                </select>
                                @if ($errors->has('user'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('user') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        {{-- Reason --}}
                        <div class="form-group{{ $errors->has('reason') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="reason">Reason</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="reason" id="reason" value="{{ old('reason') }}" autofocus>

                                @if ($errors->has('reason'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('reason') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group m-b-none">
                            <div class="col-sm-4 col-sm-offset-2">
                                <a class="btn btn-white" href="{{ action('TaskController@show', $task) }}">Cancel</a>
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

@section('javascript')
    <script>
        new Vue({
            el: '#users-select-wrapper',
            data: {
                users: []
            },
            methods: {
                fetchUsers: function () {
                    var that = this;
                    @if($task->template)
                        var template = '{{ $task->template->id }}';
                        $.ajax({
                            url: '/templates/' + template + '/users',
                            method: 'GET',
                            dataType: 'json',
                            success: function (response) {
                                that.users = response;

                                setTimeout(function () {
                                    $("#user").trigger("chosen:updated");
                                }, 500);
                            }
                        })
                    @endif
                }
            },
            mounted: function () {
                @if($task->template)
//                    $('#template').chosen().change(this.fetchUsers)
                    this.fetchUsers();
                @endif
            }
        })
    </script>
@stop
