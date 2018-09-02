@extends('layouts.app')

@section('title', 'Add subtask to template')

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <h2>Add subtask to template</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url('/dashboard') }}">Dashboard</a>
            </li>
            <li>
                <a href="{{ action('TemplateController@index') }}">Templates</a>
            </li>
            <li>
                <a href="{{ action('TemplateController@show', $template) }}">{{ $template->title }}</a>
            </li>
            <li>
                <a href="{{ action('TemplateController@show', $template) }}">Subtasks</a>
            </li>
            <li class="active">
                <strong>Add</strong>
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
                    <h5>Add subtask <small>To {{ $template->title }}.</small></h5>
                </div>
                <div class="ibox-content">
                    <form id="form" class="form-horizontal" role="form" method="POST" action="{{ action('TemplateSubtaskController@store', $template) }}">
                        {{ csrf_field() }}

                        {{-- Title --}}
                        <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="title">Title</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="title" id="title" value="{{ old('title') }}" required autofocus>

                                @if ($errors->has('title'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        {{-- Description --}}
                        <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="description">Description</label>

                            <div class="col-sm-10">
                                <textarea class="wysiwyg" name="description" id="description">{!! old('description') !!}</textarea>
                                @if ($errors->has('description'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('description') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        {{-- Add to existing tasks --}}
                        <div class="form-group{{ $errors->has('add-to-tasks') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="add-to-tasks">Add to tasks</label>

                            <div class="col-sm-10">
                                <input type="hidden" name="add-to-tasks" value="0" />
                                <input type="checkbox" class="js-switch" value="1" id="add-to-tasks" name="add-to-tasks" />
                                <label class="checkbox-inline">Add this subtask template to all existing tasks</label>

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
                                <a class="btn btn-white" href="{{ action('TemplateController@show', $template) }}">Cancel</a>
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
