@extends('layouts.app')

@section('title', 'Create task')

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <h2>Create task</h2>
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
            <li class="active">
                <strong>Create</strong>
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
                    <h5>Add task <small>To {{ $client->name }}.</small></h5>
                </div>
                <div class="ibox-content" id="template-form-wrapper">
                    <form id="form" class="form-horizontal" role="form" method="POST" action="{{ action('TaskController@store', $client) }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="client_id" value="{{ $client->id }}" />

                        {{-- Template --}}
                        <div class="form-group{{ $errors->has('template_id') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="template_id">Template</label>

                            <div class="col-sm-10">
                                <select class="form-control chosen-select" name="template_id" id="template_id" autofocus>
                                    @foreach (App\Repositories\Template\Template::orderBy('title')->get() as $template)
                                        <option value="{{ $template->id }}"{{ (old('template_id') == $template->id) ? ' selected' : '' }}>{{ $template->title }} ({{ $template->category }})</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('template_id'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('template_id') }}</strong>
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
                                    <option value="0" {{ old('private') == 0 ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('private') == 1 ? 'selected' : '' }}>Yes</option>
                                </select>

                                @if ($errors->has('private'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('private') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        {{-- User --}}
                        <div class="form-group{{ $errors->has('user_id') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="user_id">User</label>

                            <div class="col-sm-10">
                                <select class="form-control chosen-select" name="user_id" id="user_id">
                                    <option v-if="! users.length">No users available</option>
                                    <option v-else>Select user...</option>
                                    <option :selected="user.id == {{ old('user', 0) }}" :value="user.id" v-if="users.length" v-for="user in users" v-text="user.name"></option>
                                </select>

                                @if ($errors->has('user_id'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('user_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        {{-- Deadline --}}
                        <div class="form-group{{ $errors->has('deadline') ? ' has-error' : '' }}">

                            {{-- This field is made from  the 3 fields below. This is the one used in validations --}}
                            <input type="hidden" name="deadline" id="deadline" value="" />

                            {{-- Deadline Type --}}
                            <label class="col-sm-2 control-label" for="deadline_type">Deadline Type</label>

                            <div class="col-sm-10 m-b-md">
                                <select id="deadline_type" class="form-control no-border-radius" name="deadline_type">
                                    <option value="date">Date</option>
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
                                    <input type="text" class="form-control" name="deadline_date" id="deadline_date" value="{{ old('deadline_date') }}">
                                </div>
                            </div>

                            {{-- Deadline Time --}}
                            <div id="deadline_time_group" style="display:none">
                                <label class="col-sm-2 control-label" for="deadline_time">Deadline Time</label>

                                <div class="col-sm-10">
                                    <input id="deadline_time" type="text" class="form-control" data-mask="99:99" placeholder="12:00" name="deadline_time" value="23:59" disabled>
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
                                <input type="checkbox" class="js-switch" value="1" id="repeating" name="repeating"{{ (old('repeating') == 1) ? ' checked' : '' }}>

                                @if ($errors->has('repeating'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('repeating') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Frequency --}}
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="frequency">Frequency</label>

                            {{-- This field is made from  the 3 fields below. This is the one used in validations --}}
                            <input type="hidden" name="frequency" id="frequency" value="" />

                            <div class="col-sm-3">
                                <div class="input-group m-b">
                                    <span class="input-group-addon">Repeat every</span>
                                    <select id="frequency-what" class="form-control no-border-radius" name="frequency-builder[what]">
                                        <option value="months">Month</option>
                                        <option value="weeks">Week</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="input-group m-b">
                                    <span class="input-group-addon">How often</span>
                                    <select data-type='months' class="form-control no-border-radius" name="frequency-builder[nth]">
                                        <option value="1">Every month</option>
                                        <option value="2">Every second month</option>
                                        <option value="4">Every fourth month</option>
                                        <option value="6">Every sixth month</option>
                                        <option value="12">Every twelfth month</option>
                                    </select>
                                    <select data-type='weeks' class="form-control no-border-radius" name="frequency-builder[nth]" style="display:none;" disabled>
                                        <option value="1">Every week</option>
                                        <option value="2">Every second week</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="input-group m-b">
                                    <span class="input-group-addon">Day</span>
                                    <select data-type='months' class="form-control no-border-radius" name="frequency-builder[at]">
                                        @for($i=1; $i<=28; $i++)
                                            <option value="{{$i}}">{{$i}}</option>
                                        @endfor
                                        <option value="end">Month end</option>
                                    </select>

                                    <select data-type='weeks' class="form-control no-border-radius" name="frequency-builder[at]" style="display:none;" disabled>
                                        <option value="1">Monday</option>
                                        <option value="2">Tuesday</option>
                                        <option value="3">Wednesday</option>
                                        <option value="4">Thursday</option>
                                        <option value="5">Friday</option>
                                        <option value="6">Saturday</option>
                                        <option value="0">Sunday</option>
                                    </select>
                                </div>
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
                                    <input type="text" class="form-control" name="end_date" id="end_date" value="{{ old('end_date') }}" placeholder="Only use this datefield if the task should stop repeating on a specific date.">
                                </div>

                                @if ($errors->has('end_date'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('end_date') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group m-b-none">
                            <div class="col-sm-4 col-sm-offset-2">
                                <a class="btn btn-white" href="{{ action('ClientController@show', $client) }}">Cancel</a>
                                <button class="btn btn-primary" type="submit">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script type="text/javascript">
        $( document ).ready(function() {
            // Change fields based on frequency type
            $('#frequency-what').on('change', function() {
                if( $(this).val() == "weeks" ){
                    $('select[data-type="months"]').prop('disabled', true).hide();
                    $('select[data-type="weeks"]').prop('disabled', false).show();
                }
                else {
                    $('select[data-type="months"]').prop('disabled', false).show();
                    $('select[data-type="weeks"]').prop('disabled', true).hide();
                }
            });

            // Change default value based on deadline type
            $('#deadline_type').on('change', function() {
                if( $(this).val() == "date" ){
                    $('#deadline_time_group').hide();
                    $('#deadline_time').prop('disabled', true).val('23:59');
                }
                else {
                    $('#deadline_time_group').show();
                    $('#deadline_time').prop('disabled', false).val('');
                }
            });

            $("#form").submit(function(e){
                // Stop form submition
                e.preventDefault();

                // Build frequency
                var frequency = $('select:not([disabled])[name="frequency-builder[nth]"]').val() + ' ' + $('select[name="frequency-builder[what]"]').val() + ' ' + $('select:not([disabled])[name="frequency-builder[at]"]').val();
                $('#frequency').val(frequency);

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
            el: '#template-form-wrapper',
            data: {
                users: []
            },
            methods: {
                fetchUsers: function () {
                    var that = this;
                    var template = $('#template_id').val();

                    if (typeof template == 'undefined') {
                        return;
                    }

                    $.ajax({
                        url: '/templates/' + template + '/users',
                        method: 'GET',
                        dataType: 'json',
                        success: function (response) {
                            that.users = response;
                            setTimeout(function () {
                                $("#user_id").trigger("chosen:updated");
                            }, 500);
                        }
                    })
                }
            },
            mounted: function () {
                $('#template_id').chosen().change(this.fetchUsers)
                this.fetchUsers();
            }
        })
    </script>
@stop
