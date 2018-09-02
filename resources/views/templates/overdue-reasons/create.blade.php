@extends('layouts.app')

@section('title', 'Add template overdue reason')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>Add overdue reason to template</h2>
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
                <li class="active"><strong>Add</strong></li>
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
                        <form id="form" class="form-horizontal" role="form" method="POST" action="{{ action('TemplateOverdueReasonController@store', $template) }}">
                            {{ csrf_field() }}
                            <input type="hidden" name="template_id" value="{{ $template->id }}" />

                            {{-- Overdue reason --}}
                            <div class="form-group{{ $errors->has('overdue_reason_id') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="overdue_reason_id">Overdue reason</label>

                                <div class="col-sm-10">
                                    <select class="form-control chosen-select" name="overdue_reason_id" id="overdue_reason_id" onchange="updateTypes()">
                                        <option disabled value="">Select an overdue reason</option>
                                        @foreach ($availableOverdueReasons as $availableOverdueReasonId => $availableOverdueReason)
                                            <option value="{{ $availableOverdueReasonId }}" {{ old('overdue_reason_id') == $availableOverdueReasonId ? 'selected' : '' }}>{{ $overdueReasonNames[$availableOverdueReasonId] }}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('overdue_reason_id'))
                                        <span class="help-block m-b-none"><strong>{{ $errors->first('overdue_reason_id') }}</strong></span>
                                    @endif
                                </div>
                            </div>

                            {{-- Trigger type --}}
                            <div class="form-group{{ $errors->has('trigger_type') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="trigger_type">Trigger type</label>
                                <div class="col-sm-3">
                                    <select class="form-control" name="trigger_type" id="trigger_type" onchange="">
                                        @foreach ($triggerTypes as $triggerKey => $triggerName)
                                            <option value="{{ $triggerKey }}" {{ old('trigger_type') == $triggerKey ? 'selected' : '' }}>{{ $triggerName }}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('trigger_type'))
                                        <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('trigger_type') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Trigger counter --}}
                            <div class="form-group{{ $errors->has('trigger_counter') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="trigger_counter">Trigger counter</label>

                                <div class="col-sm-3">
                                    <input type="number" min="0" step="1" class="form-control" name="trigger_counter" id="trigger_counter" value="{{ old('trigger_counter') }}" />
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
                                            <option value="{{ $triggerActionKey }}" {{ old('action') == $triggerActionKey ? 'selected' : '' }}>{{ $triggerActionName }}</option>
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
        var $reasonTriggerTypes = '<?php echo json_encode($availableOverdueReasons) ?>';
        var $oldTriggerType = '<?php echo old('trigger_type') ?>';

        function updateTypes(defaultTriggerType) {
            var reasonId = $('#overdue_reason_id').val();
            var reasonTypes = $.parseJSON($reasonTriggerTypes);

            $('#trigger_type').html('');
            $.each(reasonTypes[reasonId], function(key, value)
            {
                $('#trigger_type').append('<option value=' + key + '>' + value + '</option>');
            });

            $("#trigger_type").val(defaultTriggerType);
        }

        $( document ).ready(function() {
            updateTypes($oldTriggerType);
        });
    </script>
@append
