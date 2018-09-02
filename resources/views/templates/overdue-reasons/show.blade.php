@extends('layouts.app')

@section('title', 'Edit template overdue reason')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>Edit template overdue reason</h2>
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
                <li>Template overdue reasons</li>
                <li>{{ $templateOverdueReason->overdueReason->reason  }}</li>
                <li class="active"><strong>Edit</strong></li>
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
                        <h5>Add overdue reason <small>to {{ $template->title }}.</small></h5>
                    </div>
                    <div class="ibox-content">
                        <form id="form" class="form-horizontal" role="form" method="POST" action="{{ action('TemplateOverdueReasonController@update', [$template, $templateOverdueReason]) }}">
                            {{ csrf_field() }}

                            {{-- Overdue reason --}}
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="overdue_reason_id">Overdue reason</label>
                                <div class="col-sm-10">
                                    <p class="form-control-static">{{ $templateOverdueReason->overdueReason->reason }}</p>
                                </div>
                            </div>

                            {{-- Trigger type --}}
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="trigger_type">Trigger type</label>
                                <div class="col-sm-3">
                                    <p class="form-control-static">{{ $templateOverdueReason->trigger_type }}</p>
                                </div>
                            </div>

                            {{-- Trigger counter --}}
                            <div class="form-group{{ $errors->has('trigger_counter') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="trigger_counter">Trigger counter</label>

                                <div class="col-sm-10">
                                    <input type="number" min="0" step="1" class="form-control" name="trigger_counter" id="trigger_counter" value="{{ old('trigger_counter', $templateOverdueReason->trigger_counter) }}" />
                                    @if ($errors->has('trigger_counter'))
                                        <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('trigger_counter') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Action --}}
                            <div class="form-group{{ $errors->has('action') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="action">Action</label>

                                <div class="col-sm-10">
                                    <select class="form-control chosen-select" name="action" id="action" onchange="">
                                        @foreach ($triggerActions as $triggerActionKey => $triggerActionName)
                                            <option value="{{ $triggerActionKey }}" {{ old('action', $templateOverdueReason->action) == $triggerActionKey ? 'selected' : '' }}>{{ $triggerActionName }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('action'))
                                        <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('action') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group m-b-none">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <a class="btn btn-white" href="{{ action('TemplateController@show', $template) }}">Cancel</a>
                                    <button class="btn btn-primary" type="submit">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

