@extends('layouts.app')

@section('title', 'Create ' . $task->title)

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <h2>Edit {{ $task->title }}</h2>
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
                    <h5>Edit {{ $task->title }} <small>In {{ $client->name }}.</small></h5>
                </div>
                <div class="ibox-content">
                    <form id="form" class="form-horizontal" role="form" method="POST" action="{{ action('TaskController@update', $task) }}">
                        {{ csrf_field() }}

                        {{-- Title --}}
                        <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="title">Title</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="title" id="title" value="{{ $task->title }}">

                                @if ($errors->has('title'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        {{-- Private --}}
                        <div class="form-group{{ $errors->has('private') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="private">Private</label>

                            <div class="col-sm-10">
                                <select class="form-control chosen-select" name="private" id="private" autofocus>
                                    <option value="0" {{ old('private', $task->private) == 0 ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('private', $task->private) == 1 ? 'selected' : '' }}>Yes</option>
                                </select>

                                @if ($errors->has('private'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('private') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        {{-- Description --}}
                        @if($task->isCustom())
                            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="description">Description</label>

                                <div class="col-sm-10">
                                    <textarea class="wysiwyg" name="description" id="description">{!! $task->details->description !!}</textarea>
                                    @if ($errors->has('description'))
                                        <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('description') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                        @else
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="description">Description</label>
                                <div class="col-sm-10 form-control-static">
                                    {!! $task->version->description !!}
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                        @endif


                        {{-- User --}}
                        <div class="form-group{{ $errors->has('user') ? ' has-error' : '' }}" id="users-select-wrapper">
                            <label class="col-sm-2 control-label" for="user">User</label>

                            <div class="col-sm-10">
                                @if($task->template)
                                    <select class="form-control chosen-select" name="user" id="user">
                                        <option v-if="! users.length">No users available</option>
                                        <option v-else>Select user...</option>
                                        <option :selected="user.id == {{ old('user', $task->user_id ?? 0) }}" :value="user.id" v-if="users.length" v-for="user in users">@{{ user.name }}</option>
                                    </select>
                                @else
                                    <select class="form-control chosen-select" name="user" id="user">
                                        @if(Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                                            @foreach (App\Repositories\User\User::active()->orderBy('name')->get() as $user)
                                                <option value="{{ $user->id }}"{{ (old('user') == $user->id) ? ' selected' : ((isset($task->user) && ($user->id == $task->user->id)) ? ' selected' : '') }}>{{ $user->name }}</option>
                                            @endforeach
                                        @else
                                            @foreach (App\Repositories\User\User::canHaveCustomTask($client)->active()->orderBy('name')->get() as $user)
                                                <option value="{{ $user->id }}"{{ (old('user') == $user->id) ? ' selected' : ((isset($task->user) && ($user->id == $task->user->id)) ? ' selected' : '') }}>{{ $user->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                @endif
                                @if ($errors->has('user'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('user') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                            {{-- Deadline --}}
                            <div class="form-group{{ $errors->has('deadline') ? ' has-error' : '' }}">

                                {{-- This field is made from  the 3 fields below. This is the one used in validations --}}
                                <input type="hidden" name="deadline" id="deadline" value="" />

                                {{-- Deadline Type --}}
                                <label class="col-sm-2 control-label" for="deadline_type">Deadline Type</label>

                                <div class="col-sm-10 m-b-md">
                                    <select id="deadline_type" class="form-control no-border-radius" name="deadline_type">
                                        <option {{ $task->deadline->toTimeString() != '23:59:00'? 'selected' : '' }} value="date">Date</option>
                                        <option value="datetime">DateTime</option>
                                    </select>
                                </div>

                                {{-- Deadline Date --}}
                                <label class="col-sm-2 control-label" for="deadline_date">Deadline Date</label>

                                <div class="col-sm-10 m-b-md">
                                    <div id="deadline_wrapper" class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                                        <span class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                        <input type="text" class="form-control" name="deadline_date" id="deadline_date" value="{{ old('deadline_date', $task->deadline->toDateString()) }}">
                                    </div>
                                </div>

                                {{-- Deadline Time --}}
                                <div id="deadline_time_group" {!! $task->deadline->toTimeString() == '23:59:00'? 'style="display:none;"' : '' !!}>
                                    <label class="col-sm-2 control-label" for="deadline_time">Deadline Time</label>

                                    <div class="col-sm-10">
                                        <input id="deadline_time" type="text" class="form-control" data-mask="99:99" placeholder="12:00" name="deadline_time" value="{{ old('deadline_time', $task->deadline->format('H:i')) }}" >
                                        <span class="help-block">In the format HH:MM. For example: <code>15:00</code> if the task needs to be completed before 15:00.</span>
                                    </div>
                                </div>

                                <div class="col-sm-10 col-sm-offset-2">
                                    @if ($errors->has('deadline'))
                                        <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('deadline') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>

                            {{-- Repeating --}}
                            <div class="form-group{{ $errors->has('repeating') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="repeating">Repeating</label>

                                <div class="col-sm-10">
                                    <input type="hidden" name="repeating" value="0">
                                    <input type="checkbox" class="js-switch" value="1" id="repeating" name="repeating"{{ ($task->repeating) ? ' checked' : '' }}>

                                    @if ($errors->has('repeating'))
                                        <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('repeating') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Frequency --}}
                            <div class="form-group{{ $errors->has('frequency') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="frequency">Frequency</label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="frequency" id="frequency" value="{{ $task->frequency }}">

                                    @if ($errors->has('frequency'))
                                        <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('frequency') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            {{-- End by date --}}
                            <div class="form-group{{ $errors->has('end_date') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="end_date">End by date</label>

                                <div class="col-sm-10">
                                    <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                                        <span class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                        <input type="text" class="form-control" name="end_date" id="end_date" value="{{ ($task->end_date) ? $task->end_date->format('Y-m-d') : '' }}" placeholder="Only use this datefield if the task should stop repeating on a specific date.">
                                    </div>

                                    @if ($errors->has('end_date'))
                                        <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('end_date') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                        @endif
                        <div class="form-group m-b-none">
                            <div class="col-sm-4 col-sm-offset-2">
                                <a class="btn btn-white" href="{{ action('TaskController@show', $task) }}">Cancel</a>
                                <button class="btn btn-primary" type="submit">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Delete task form --}}
            @can('delete', $task)
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Danger Zone</h5>
                    </div>
                    <div class="ibox-content">
                        <form role="form" method="POST" action="{{ action('TaskController@destroy', $task) }}">
                            {{ csrf_field() }}

                            <input type="hidden" name="_method" value="DELETE">

                            <button class="btn btn-danger btn-outline" type="submit">Delete task</button>
                        </form>
                    </div>
                </div>
            @endcan
        </div>
    </div>
</div>
@endsection

@section('script')
    <script type="text/javascript">
        $( document ).ready(function() {

            // Change default value based on deadline type
            $('#deadline_type').on('change', function() {
                if( $(this).val() == "date" ){
                    $('#deadline_time_group').hide();
                    $('#deadline_time').prop('disabled', true);
                }
                else {
                    $('#deadline_time_group').show();
                    $('#deadline_time').prop('disabled', false);
                }
            });

            $("#form").submit(function(e){
                // Stop form submition
                e.preventDefault();

                // Build deadline
                var deadline = $('#deadline_date').val() + ' ' + $('#deadline_time').val() + ':00';
                $('#deadline').val(deadline);

                // Submit form
                this.submit();
            });
        });
    </script>
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
                    @endif
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
                }
            },
            mounted: function () {
                @if($task->template)
                    $('#template').chosen().change(this.fetchUsers)
                    this.fetchUsers();
                @endif
            }
        })
    </script>
@append
