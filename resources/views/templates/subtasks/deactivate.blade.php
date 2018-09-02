@extends('layouts.app')

@section('title', 'Deactivate subtask template')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>Deactivate subtask template</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                    <a href="{{ action('TemplateController@index') }}">Templates</a>
                </li>
                <li>
                    <a href="{{ action('TemplateController@show', $templateSubtask->template) }}">{{ $templateSubtask->template->title }}</a>
                </li>
                <li>
                    <a href="{{ action('TemplateController@show', $templateSubtask->template) }}">Subtasks</a>
                </li>
                <li class="active">
                    <strong>Deactivate</strong>
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
                        <h5>Delete subtask {{ $templateSubtask->title }}</h5>
                    </div>
                    <div class="ibox-content">
                        <form id="form" class="form-horizontal" role="form" method="POST" action="{{ action('TemplateSubtaskController@deactivate', $templateSubtask) }}">
                            {{ csrf_field() }}

                            {{-- Add to existing tasks --}}
                            <div class="form-group{{ $errors->has('add-to-tasks') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="add-to-tasks">Remove from tasks</label>

                                <div class="col-sm-10">
                                    <input type="hidden" name="add-to-tasks" value="0" />
                                    <input type="checkbox" class="js-switch" value="1" id="add-to-tasks" name="add-to-tasks" />
                                    <label class="checkbox-inline">Delete this subtask template from all existing tasks</label>

                                    @if ($errors->has('add-to-tasks'))
                                        <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('add-to-tasks') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            {{-- Alter tasks after this date (if add_to_tasks==1) --}}
                            <div id="min-date-wrapper" style="display:none;">
                                <div class="form-group{{ $errors->has('min-date') ? ' has-error' : '' }}">
                                    <label class="col-sm-2 control-label" for="min-date">From date</label>

                                    <div class="col-sm-2">
                                        <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                                        <span class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                            <input type="text" class="form-control" name="min-date" id="min-date" value="{{ old('min-date') }}">
                                        </div>

                                        @if ($errors->has('min-date'))
                                            <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('min-date') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">If selected it will alter tasks that have deadline AFTER this date</p>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                            </div>


                            <div class="form-group m-b-none">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <a class="btn btn-white" href="{{ action('TemplateController@show', $templateSubtask->template) }}">Cancel</a>
                                    <button class="btn btn-danger" type="submit">Deactivate</button>
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
            $('#add-to-tasks').change(function() {
                if($(this).is(":checked")) {
                    $('#min-date-wrapper').show();
                }
                else {
                    $('#min-date-wrapper').hide();
                }
            });
        });
    </script>
@append
