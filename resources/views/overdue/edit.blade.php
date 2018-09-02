@extends('layouts.app')

@section('title', 'Edit reason')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2>Clients</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                    <a href="{{ action('OverdueReasonController@index') }}">Overdue Reasons</a>
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
                        <h5>Add reason</h5>
                    </div>
                    <div class="ibox-content">
                        <form  id="form" class="form-horizontal" role="form" method="POST" action="{{ action('OverdueReasonController@update', $reason) }}">
                            {{ csrf_field() }}

                            {{-- Reason --}}
                            <div class="form-group{{ $errors->has('reason') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="reason">Reason</label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="reason" id="reason" value="{{ $reason->reason }}">

                                    @if ($errors->has('reason'))
                                        <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('reason') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Description --}}
                            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="description">Description</label>

                                <div class="col-sm-10">
                                    <textarea class="wysiwyg" name="description" id="description">{{ old('description', $reason->description) }}</textarea>
                                    @if ($errors->has('description'))
                                        <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('description') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Days --}}
                            <div class="form-group{{ $errors->has('days') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="reason">Days</label>

                                <div class="col-sm-10">
                                    <input type="number" class="form-control" name="days" id="days" value="{{ $reason->days }}">

                                    @if ($errors->has('days'))
                                        <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('days') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>

                            {{-- Complete tasks --}}
                            <div class="form-group{{ ($errors->has('complete_task') || ($errors->has('completed_user_id')) ) ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="complete_task">Complete task</label>
                                <div class="col-sm-1">
                                    <input type="hidden" name="complete_task" value="0" />
                                    <input type="checkbox" class="js-switch" value="1" id="complete_task" name="complete_task" {{ (old('complete_task', $reason->complete_task)) ? ' checked' : '' }}>

                                    @if ($errors->has('complete_task'))
                                        <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('complete_task') }}</strong>
                                    </span>
                                    @endif
                                </div>


                                <label class="col-sm-2 control-label completed-users-item" for="complete_task" style="{{!old('complete_task', $reason->complete_task) ? 'display:none' : ''}}">Completed user</label>
                                <div class="col-sm-3 completed-users-item" style="{{!old('complete_task', $reason->complete_task) ? 'display:none' : ''}}">
                                    <select class="form-control chosen-select chosen-select-hidden" name="completed_user_id" id="completed_user_id">
                                        <option value=""></option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}" {{ (old('completed_user_id', $reason->completed_user_id) == $user->id) ? 'selected' : '' }} >{{ $user->name }}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('completed_user_id'))
                                        <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('completed_user_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Required --}}
                            <div class="form-group{{ $errors->has('required') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="required">Required</label>

                                <div class="col-sm-10">
                                    <input type="hidden" name="required" value="0" />
                                    <input type="checkbox" class="js-switch" value="1" id="required" name="required" {{ ($reason->required) ? ' checked' : '' }}>

                                    @if ($errors->has('required'))
                                        <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('required') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Color --}}
                            <div class="form-group{{ $errors->has('hex') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="hex">Color</label>

                                <div class="col-sm-10">
                                    <input type="text" name="hex", id="hex" class="form-control colorpicker-element" value="{{ $reason->hex ? $reason->hex : '#ED5565' }}" />

                                    @if ($errors->has('hex'))
                                        <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('hex') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Default --}}
                            <div class="form-group{{ $errors->has('default') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="default">Default</label>

                                <div class="col-sm-10">
                                    <input type="hidden" name="default" value="0" />
                                    <input type="checkbox" class="js-switch" value="1" id="default" name="default" {{ ($reason->default) ? ' checked' : '' }}>

                                    @if ($errors->has('default'))
                                        <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('default') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Visible --}}
                            <div class="form-group{{ $errors->has('visible') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="visible">Visible</label>

                                <div class="col-sm-10">
                                    <input type="hidden" name="visible" value="0" />
                                    <input type="checkbox" class="js-switch" value="1" id="visible" name="visible" {{ ($reason->visible) ? ' checked' : '' }}>

                                    @if ($errors->has('visible'))
                                        <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('visible') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Is visible in report --}}
                            <div class="form-group{{ $errors->has('is_visible_in_report') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="is_visible_in_report">Is visible in report</label>

                                <div class="col-sm-10">
                                    <input type="hidden" name="is_visible_in_report" value="0" />
                                    <input type="checkbox" class="js-switch" value="1" id="is_visible_in_report" name="is_visible_in_report" {{ ($reason->is_visible_in_report) ? ' checked' : '' }}>

                                    @if ($errors->has('is_visible_in_report'))
                                        <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('is_visible_in_report') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Report threshold value --}}
                            <div class="form-group{{ $errors->has('threshold_value') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="threshold_value">Report threshold value</label>

                                <div class="col-sm-10">
                                    <input type="number" min="0" class="form-control" name="threshold_value" id="threshold_value" value="{{ (int) $reason->threshold_value }}">

                                    @if ($errors->has('threshold_value'))
                                        <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('threshold_value') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>

                            <div class="form-group m-b-none">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <a class="btn btn-white" href="{{ action('OverdueReasonController@index') }}">Cancel</a>
                                    <button class="btn btn-primary" type="submit">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                {{-- Delete task form --}}
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Danger Zone</h5>
                    </div>
                    <div class="ibox-content">
                        <form role="form" method="POST" action="{{ action('OverdueReasonController@delete', $reason) }}">
                            {{ csrf_field() }}

                            <input type="hidden" name="_method" value="DELETE">
                            <input type="hidden" name="active" value="{{ $reason->active }}">

                            <button class="btn btn-danger btn-outline" type="submit">{{ $reason->active ? 'Deactivate reason' : 'Activate reason' }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script type="text/javascript">
        $("#complete_task").on("change", function(){
            if ($(this).is(":checked")) {
                $(".completed-users-item").show();
            } else {
                $(".completed-users-item").hide();
            }

            $(".completed-users-item").find(".chosen-select-hidden").chosen({
                allow_single_deselect: true,
                width: "100%",
            });
        });
    </script>
@endsection