@extends('layouts.app')

@section('title', 'Create contract')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>Create contract</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                    <a href="{{ route('clients.index') }}">Clients</a>
                </li>
                <li>
                    <a href="{{ route('client.show', $client) }}">{{ $client->name }}</a>
                </li>
                <li>
                    Contracts
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
                        <h5>Create contract <small>Add a new contract to {{ $client->name }}</small></h5>
                    </div>
                    <div class="ibox-content">
                        <form id="form" class="form-horizontal" role="form" method="POST" action="{{ route('contracts.preview') }}">
                            {{ csrf_field() }}

                            {{-- Client --}}
                            <div class="form-group{{ $errors->has('client_id') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="client_id">Client</label>
                                <div class="col-sm-10">
                                    <input type="hidden" name="client_id" value="{{ $client->id }}" />
                                    <p class="form-control-static">{{ $client->name }}</p>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            {{-- Start date --}}
                            <div class="form-group{{ $errors->has('start_date') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="start_date">Start date</label>
                                <div class="col-sm-3">
                                    <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" class="form-control" name="start_date" id="start_date" value="{{ old('start_date', \Carbon\Carbon::now()->format('Y-m-d')) }}">
                                    </div>
                                    @if ($errors->has('start_date'))
                                        <span class="help-block m-b-none"><strong>{{ $errors->first('start_date') }}</strong></span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            {{-- One time contract --}}
                            <div id="one_time_wrapper">
                                <div class="form-group{{ $errors->has('one_time') ? ' has-error' : '' }}">
                                    <label class="col-sm-2 control-label" for="one_time">One time</label>
                                    <div class="col-sm-2">
                                        <input type="hidden" name="one_time" value="0" />
                                        <input type="checkbox" class="js-switch" id="one_time" name="one_time" value="1" {{old('one_time', $oldContract->one_time) ? 'checked' : ''}} />
                                        @if ($errors->has('one_time'))
                                            <span class="help-block m-b-none"><strong>{{ $errors->first('one_time') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                            </div>

                            {{-- Under 50 bills per year --}}
                            <div id="under_50_bills_wrapper">
                                <div class="form-group{{ $errors->has('under_50_bills') ? ' has-error' : '' }}">
                                    <label class="col-sm-2 control-label" for="under_50_bills">Under 50 bills</label>
                                    <div class="col-sm-2">
                                        <input type="hidden" name="under_50_bills" value="0" />
                                        <input type="checkbox" class="js-switch" id="under_50_bills" name="under_50_bills" value="1" {{old('under_50_bills', $oldContract->under_50_bills) ? 'checked' : ''}} />
                                        @if ($errors->has('under_50_bills'))
                                            <span class="help-block m-b-none"><strong>{{ $errors->first('under_50_bills') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                            </div>

                            {{-- Shareholder Registry --}}
                            <div id="shareholder_registry_wrapper">
                                <div class="form-group{{ $errors->has('shareholder_registry') ? ' has-error' : '' }}">
                                    <label class="col-sm-2 control-label" for="shareholder_registry">Shareholder Registry</label>
                                    <div class="col-sm-2">
                                        <input type="hidden" name="shareholder_registry" value="0" />
                                        <input type="checkbox" class="js-switch" id="shareholder_registry" name="shareholder_registry" value="1" {{old('shareholder_registry', $oldContract->shareholder_registry) ? 'checked' : ''}} />
                                        @if ($errors->has('shareholder_registry'))
                                            <span class="help-block m-b-none"><strong>{{ $errors->first('shareholder_registry') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                            </div>

                            {{-- Control Client --}}
                            <div id="control_client_wrapper">
                                <div class="form-group{{ $errors->has('control_client') ? ' has-error' : '' }}">
                                    <label class="col-sm-2 control-label" for="control_client">Control of accounting</label>
                                    <div class="col-sm-2">
                                        <input type="hidden" name="control_client" value="0" />
                                        <input type="checkbox" class="js-switch" id="control_client" name="control_client" value="1" {{old('control_client', $oldContract->control_client) ? 'checked' : ''}} />
                                        @if ($errors->has('control_client'))
                                            <span class="help-block m-b-none"><strong>{{ $errors->first('control_client') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                            </div>

                            {{-- Bank Reconciliation --}}
                            <div id="bank_reconciliation_wrapper">
                                <div class="form-group{{ $errors->has('bank_reconciliation') || $errors->has('bank_reconciliation_date') ? ' has-error' : '' }}">
                                    <label class="col-sm-2 control-label" for="bank_reconciliation">Bank Reconciliation</label>
                                    <div class="col-sm-1">
                                        <input type="hidden" name="bank_reconciliation" value="0" />
                                        <input type="checkbox" class="js-switch" id="bank_reconciliation" name="bank_reconciliation" value="1" {{old('bank_reconciliation', $oldContract->bank_reconciliation) ? 'checked' : ''}} />
                                        @if ($errors->has('bank_reconciliation'))
                                            <span class="help-block m-b-none"><strong>{{ $errors->first('bank_reconciliation') }}</strong></span>
                                        @endif
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="input-group m-b date" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" class="form-control" name="bank_reconciliation_date" id="bank_reconciliation_date" value="{{ old('bank_reconciliation_date', $oldContract->bank_reconciliation_date) }}">
                                            <span class="input-group-addon">From</span>
                                        </div>
                                        @if ($errors->has('bank_reconciliation_date'))
                                            <span class="help-block m-b-none"><strong>{{ $errors->first('bank_reconciliation_date') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                            </div>

                            {{-- Bank Reconciliation frequency --}}
                            <div id="bank_reconciliation_frequency_wrapper">
                                <div class="form-group{{ $errors->has('bank_reconciliation_frequency') || $errors->has('bank_reconciliation_frequency_custom') ? ' has-error' : '' }}">
                                    <label class="col-sm-2 control-label" for="bank_reconciliation_frequency">Custom Bank Reconciliation</label>
                                    <div class="col-sm-1">
                                        <input type="hidden" name="bank_reconciliation_frequency_custom" value="0" />
                                        <input type="checkbox" class="js-switch" id="bank_reconciliation_frequency_custom" name="bank_reconciliation_frequency_custom" value="1" />
                                        @if ($errors->has('bank_reconciliation_frequency_custom'))
                                            <span class="help-block m-b-none"><strong>{{ $errors->first('bank_reconciliation_frequency_custom') }}</strong></span>
                                        @endif
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="input-group m-b">
                                            <span class="input-group-addon">Frequency</span>
                                            <select data-type='months' class="form-control no-border-radius" name="bank_reconciliation_frequency">
                                                <option value="1 months 10" {{ old('bank_reconciliation_frequency', $oldContract->bank_reconciliation_frequency) == '1 months 10' ? 'selected' : '' }}>Every month</option>
                                                <option value="2 months 10" {{ old('bank_reconciliation_frequency', $oldContract->bank_reconciliation_frequency) == '2 months 10' ? 'selected' : '' }}>Every second month</option>
                                                <option value="3 months 10" {{ old('bank_reconciliation_frequency', $oldContract->bank_reconciliation_frequency) == '3 months 10' ? 'selected' : '' }}>Every third month</option>
                                                <option value="4 months 10" {{ old('bank_reconciliation_frequency', $oldContract->bank_reconciliation_frequency) == '4 months 10' ? 'selected' : '' }}>Every fourth month</option>
                                            </select>
                                        </div>
                                        @if ($errors->has('bank_reconciliation_frequency'))
                                            <span class="help-block m-b-none"><strong>{{ $errors->first('bank_reconciliation_frequency') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                            </div>

                            {{-- Bookkeeping --}}
                            <div id="bookkeeping_wrapper">
                                <div class="form-group{{ $errors->has('bookkeeping') || $errors->has('bookkeeping_date') ? ' has-error' : '' }}">
                                    <label class="col-sm-2 control-label" for="bank_reconciliation">Bookkeeping</label>
                                    <div class="col-sm-1">
                                        <input type="hidden" name="bookkeeping" value="0" />
                                        <input type="checkbox" class="js-switch" id="bookkeeping" name="bookkeeping" value="1" {{old('bookkeeping', $oldContract->bookkeeping) ? 'checked' : ''}} />
                                        @if ($errors->has('bookkeeping'))
                                            <span class="help-block m-b-none"><strong>{{ $errors->first('bookkeeping') }}</strong></span>
                                        @endif
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="input-group m-b date" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" class="form-control" name="bookkeeping_date" id="bookkeeping_date" value="{{ old('bookkeeping_date', $oldContract->bookkeeping_date) }}">
                                            <span class="input-group-addon">From</span>
                                        </div>
                                        @if ($errors->has('bookkeeping_date'))
                                            <span class="help-block m-b-none"><strong>{{ $errors->first('bookkeeping_date') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                            </div>

                            {{-- Bookkeeping frequency--}}
                            <div id="bookkeeping_frequency_wrapper">
                                <div class="form-group{{ $errors->has('bookkeeping_frequency_custom') || $errors->has('bookkeeping_frequency_1') || $errors->has('bookkeeping_frequency_2') ? ' has-error' : '' }}">
                                    <label class="col-sm-2 control-label" for="bank_reconciliation">Custom Bookkeeping</label>
                                    <div class="col-sm-1">
                                        <input type="hidden" name="bookkeeping_frequency_custom" value="0" />
                                        <input type="checkbox" class="js-switch" id="bookkeeping_frequency_custom" name="bookkeeping_frequency_custom" value="1" />
                                        @if ($errors->has('bookkeeping_frequency_custom'))
                                            <span class="help-block m-b-none"><strong>{{ $errors->first('bookkeeping_frequency_custom') }}</strong></span>
                                        @endif
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="input-group m-b">
                                            <span class="input-group-addon">Frequency (A jour)</span>
                                            <select data-type='months' class="form-control no-border-radius" name="bookkeeping_frequency_1">
                                                <option value="1 months 15" {{ old('bookkeeping_frequency_1', $oldContract->bookkeeping_frequency_1) == '1 months 15' ? 'selected' : '' }}>Every month</option>
                                                <option value="2 months 15" {{ old('bookkeeping_frequency_1', $oldContract->bookkeeping_frequency_1) == '2 months 15' ? 'selected' : '' }}>Every second month</option>
                                                <option value="3 months 15" {{ old('bookkeeping_frequency_1', $oldContract->bookkeeping_frequency_1) == '3 months 15' ? 'selected' : '' }}>Every third month</option>
                                                <option value="4 months 15" {{ old('bookkeeping_frequency_1', $oldContract->bookkeeping_frequency_1) == '4 months 15' ? 'selected' : '' }}>Every fourth month</option>
                                            </select>
                                        </div>
                                        @if ($errors->has('bookkeeping_frequency_1'))
                                            <span class="help-block m-b-none"><strong>{{ $errors->first('bookkeeping_frequency_1') }}</strong></span>
                                        @endif
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="input-group m-b">
                                            <span class="input-group-addon">Frequency (Bookkeeping)</span>
                                            <select data-type='months' class="form-control no-border-radius" name="bookkeeping_frequency_2">
                                                <option value="1 months 10" {{ old('bookkeeping_frequency_2', $oldContract->bookkeeping_frequency_2) == '1 months 10' ? 'selected' : '' }}>Every month</option>
                                                <option value="2 months 10" {{ old('bookkeeping_frequency_2', $oldContract->bookkeeping_frequency_2) == '2 months 10' ? 'selected' : '' }}>Every second month</option>
                                                <option value="3 months 10" {{ old('bookkeeping_frequency_2', $oldContract->bookkeeping_frequency_2) == '3 months 10' ? 'selected' : '' }}>Every third month</option>
                                                <option value="4 months 10" {{ old('bookkeeping_frequency_2', $oldContract->bookkeeping_frequency_2) == '4 months 10' ? 'selected' : '' }}>Every fourth month</option>
                                            </select>
                                        </div>
                                        @if ($errors->has('bookkeeping_frequency_2'))
                                            <span class="help-block m-b-none"><strong>{{ $errors->first('bookkeeping_frequency_2') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                            </div>

                            {{-- MVA --}}
                            <div id="mva_wrapper">
                                <div class="form-group{{ $errors->has('mva') || $errors->has('mva_type')  ? ' has-error' : '' }}">
                                    <label class="col-sm-2 control-label" for="mva">MVA</label>
                                    <div class="col-sm-1">
                                        <input type="hidden" name="mva" value="0" />
                                        <input type="checkbox" class="js-switch" id="mva" name="mva" value="1" {{old('mva', $oldContract->mva) ? 'checked' : ''}} />
                                        @if ($errors->has('mva'))
                                            <span class="help-block m-b-none"><strong>{{ $errors->first('mva') }}</strong></span>
                                        @endif
                                    </div>
                                    <div class="col-sm-4">
                                        <select class="form-control chosen-select" name="mva_type" id="mva_type">
                                            @foreach (\App\Repositories\Contract\Contract::$mvaTypes as $mvaTypeKey => $mvaTypeValue)
                                                @if($mvaTypeKey > 0) {{-- 0 is for unknown--}}
                                                    <option value="{{ $mvaTypeKey }}"{{ (old('mva_type', $oldContract->mva_type) == $mvaTypeKey) ? ' selected' : '' }}>{{ $mvaTypeValue }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        @if ($errors->has('mva_type'))
                                            <span class="help-block m-b-none"><strong>{{ $errors->first('mva_type') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                            </div>

                            {{-- Financial statements --}}
                            <div id="financial_statements_wrapper">
                                <div class="form-group{{ $errors->has('financial_statements') || $errors->has('financial_statements_year') ? ' has-error' : '' }}">
                                    <label class="col-sm-2 control-label" for="financial_statements">Financial Statements</label>
                                    <div class="col-sm-1">
                                        <input type="hidden" name="financial_statements" value="0" />
                                        <input type="checkbox" class="js-switch" id="financial_statements" name="financial_statements" value="1" {{old('financial_statements', $oldContract->financial_statements) ? 'checked' : ''}} />
                                        @if ($errors->has('financial_statements'))
                                            <span class="help-block m-b-none"><strong>{{ $errors->first('financial_statements') }}</strong></span>
                                        @endif
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="input-group m-b">
                                            <input type="number" class="form-control" name="financial_statements_year" max="{{ \Carbon\Carbon::now()->addYears(2)->year }}" id="financial_statements_year" value="{{ old('financial_statements_year') ? old('financial_statements_year') : $oldContract->financial_statements_year }}">
                                            <span class="input-group-addon">From</span>
                                        </div>
                                        @if ($errors->has('financial_statements_year'))
                                            <span class="help-block m-b-none"><strong>{{ $errors->first('financial_statements_year', $oldContract->financial_statements_year) }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                            </div>

                            {{-- Salary check --}}
                            <div id="salary_check_wrapper">
                                <div class="form-group{{ $errors->has('salary_check') ? ' has-error' : '' }}">
                                    <label class="col-sm-2 control-label" for="salary_check">Salary Check</label>
                                    <div class="col-sm-1">
                                        <input type="hidden" name="salary_check" value="0" />
                                        <input type="checkbox" class="js-switch" id="salary_check" name="salary_check" value="1" {{old('salary_check', $oldContract->salary_check) ? 'checked' : ''}} />
                                        @if ($errors->has('salary_check'))
                                            <span class="help-block m-b-none"><strong>{{ $errors->first('salary_check') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                            </div>

                            {{-- Salary --}}
                            <div id="salary_wrapper">
                                <div class="form-group{{ $errors->has('salary') ? ' has-error' : '' }}">
                                    <label class="col-sm-2 control-label" for="salary">Salary</label>
                                    <div class="col-sm-1">
                                        <input type="hidden" name="salary" value="0" />
                                        <input type="checkbox" class="js-switch" id="salary" name="salary" value="1" {{old('salary', $oldContract->salary) ? 'checked' : ''}} />
                                        @if ($errors->has('salary'))
                                            <span class="help-block m-b-none"><strong>{{ $errors->first('salary') }}</strong></span>
                                        @endif
                                    </div>
                                    <div clas="col-sm-9">
                                        <div class="row" id="salary-days-wrapper">
                                            @if($oldContract->salaryDays->count())
                                                @foreach($oldContract->salaryDays as $oldSalaryDay)
                                                    <div class="col-sm-1 salary-day">
                                                        <select class="form-control" name="salary_day[]">
                                                            @foreach ($salaryDays as $salaryDayKey => $salaryDayName)
                                                                <option value="{{ $salaryDayKey }}" {{ $oldSalaryDay->day == $salaryDayKey ? 'selected' : '' }}>{{ $salaryDayName }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="col-sm-1 salary-day">
                                                    <select class="form-control" name="salary_day[]">
                                                        @foreach ($salaryDays as $salaryDayKey => $salaryDayName)
                                                            <option value="{{ $salaryDayKey }}">{{ $salaryDayName }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif

                                            <div class="col-sm-2">
                                                <a href="#" class="btn btn-primary" id="add-salary-day"><i class="fa fa-plus"></i> Add a new day</a>
                                            </div>
                                        </div>
                                        <div id="salary-day-template" class="hidden">
                                            <div class="col-sm-1 salary-day removable">
                                                <select class="form-control" name="salary_day_template[]">
                                                    @foreach ($salaryDays as $salaryDayKey => $salaryDayName)
                                                        <option value="{{ $salaryDayKey }}">{{ $salaryDayName }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="close-icon"><i class="fa fa-times text-danger"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <span class="help-block m-t-md m-b-none">MAN = Manual salary dates</br>END = End of each month</span>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                            </div>

                            <div class="form-group m-b-none">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <a class="btn btn-white" href="{{ route('client.show', $client) }}">Cancel</a>
                                    <button class="btn btn-primary" type="submit">Continue <i class="fa fa-arrow-right m-l-xs"></i></button>
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
        $(document).ready(function() {
            $( "#add-salary-day" ).on( "click", function(e) {
                e.preventDefault();
                var template = $("#salary-day-template").html().replace('salary_day_template[]', 'salary_day[]');
                $(template).insertAfter($('#salary-days-wrapper .salary-day').last());
            });

            $( document ).on( "click", ".close-icon", function(e) {
                e.preventDefault();
                $(this).closest('.salary-day').remove();
            });

            {{-- Process the change of 'Control Client' --}}
            $('#control_client').on('change', function() {
                if($(this).is(":checked")) {
                    // Set fields (true is false in UI)
                    $("#under_50_bills").prop('checked', true).trigger('click');
                    $("#bank_reconciliation").prop('checked', true).trigger('click');
                    $("#bank_reconciliation_frequency_custom").prop('checked', true).trigger('click');
                    $("#bookkeeping").prop('checked', true).trigger('click');
                    $("#bookkeeping_frequency_custom").prop('checked', true).trigger('click');

                    // Hide elements that are supposed to remain unchecked
                    $("#under_50_bills_wrapper").hide();
                    $("#bank_reconciliation_wrapper").hide();
                    $("#bank_reconciliation_frequency_wrapper").hide();
                    $("#bookkeeping_wrapper").hide();
                    $("#bookkeeping_frequency_wrapper").hide();
                }
                else {
                    $("#under_50_bills_wrapper").show();
                    $("#bank_reconciliation_wrapper").show();
                    $("#bank_reconciliation_frequency_wrapper").show();
                    $("#bookkeeping_wrapper").show();
                    $("#bookkeeping_frequency_wrapper").show();
                }
            });

            {{-- Process the change of 'Under 50 bills' --}}
            $('#under_50_bills').on('change', function() {
                if($(this).is(":checked")) {
                    // Set fields (true is false in UI)
                    $("#control_client").prop('checked', true).trigger('click');
                    $("#bookkeeping_frequency_custom").prop('checked', true).trigger('click');
                    $("#bank_reconciliation_frequency_custom").prop('checked', true).trigger('click');
                    $("#bookkeeping").prop('checked', false).trigger('click');
                    $("#bank_reconciliation").prop('checked', false).trigger('click');
                    $("#financial_statements").prop('checked', false).trigger('click');

                    window.switcheryElements['bookkeeping'].disable();
                    window.switcheryElements['bank_reconciliation'].disable();
                    window.switcheryElements['financial_statements'].disable();

                    // Hide elements that are supposed to remain unchecked
                    $("#control_client_wrapper").hide();
                    $("#bookkeeping_frequency_wrapper").hide();
                    $("#bank_reconciliation_frequency_wrapper").hide();
                }
                else {
                    $("#control_client_wrapper").show();
                    $("#bookkeeping_frequency_wrapper").show();
                    $("#bank_reconciliation_frequency_wrapper").show();

                    window.switcheryElements['bookkeeping'].enable();
                    window.switcheryElements['bank_reconciliation'].enable();
                    window.switcheryElements['financial_statements'].enable();

                }
            });

            {{-- Process the change of 'One time contract' --}}
            $('#one_time').on('change', function() {
                if($(this).is(":checked")) {
                    // Set fields (true is false in UI)
                    $("#under_50_bills").prop('checked', true).trigger('click');
                    $("#shareholder_registry").prop('checked', true).trigger('click');
                    $("#control_client").prop('checked', true).trigger('click');
                    $("#bank_reconciliation").prop('checked', true).trigger('click');
                    $("#bank_reconciliation_frequency_custom").prop('checked', true).trigger('click');
                    $("#bookkeeping").prop('checked', true).trigger('click');
                    $("#bookkeeping_frequency_custom").prop('checked', true).trigger('click');
                    $("#mva").prop('checked', true).trigger('click');
                    $("#financial_statements").prop('checked', true).trigger('click');
                    $("#salary_check").prop('checked', true).trigger('click');
                    $("#salary").prop('checked', true).trigger('click');

                    // Hide elements
                    $("#under_50_bills_wrapper").hide();
                    $("#shareholder_registry_wrapper").hide();
                    $("#control_client_wrapper").hide();
                    $("#bank_reconciliation_wrapper").hide();
                    $("#bank_reconciliation_frequency_wrapper").hide();
                    $("#bookkeeping_wrapper").hide();
                    $("#bookkeeping_frequency_wrapper").hide();
                    $("#mva_wrapper").hide();
                    $("#financial_statements_wrapper").hide();
                    $("#salary_check_wrapper").hide();
                    $("#salary_wrapper").hide();
                }
                else {
                    $("#under_50_bills_wrapper").show();
                    $("#shareholder_registry_wrapper").show();
                    $("#control_client_wrapper").show();
                    $("#bank_reconciliation_wrapper").show();
                    $("#bank_reconciliation_frequency_wrapper").show();
                    $("#bookkeeping_wrapper").show();
                    $("#bookkeeping_frequency_wrapper").show();
                    $("#mva_wrapper").show();
                    $("#financial_statements_wrapper").show();
                    $("#salary_check_wrapper").show();
                    $("#salary_wrapper").show();
                }
            });

            {{-- Process the change of 'Salary Check' --}}
            $('#salary_check').on('change', function() {
                if($(this).is(":checked")) {
                    // Set fields (true is false in UI)
                    $("#salary").prop('checked', true).trigger('click');

                    // Hide elements that are supposed to remain unchecked
                    $("#salary_wrapper").hide();
                }
                else {
                    $("#salary_wrapper").show();
                }
            });

            {{-- Process the change of 'Salary' --}}
            $('#salary').on('change', function() {
                if($(this).is(":checked")) {
                    // Set fields (true is false in UI)
                    $("#salary_check").prop('checked', true).trigger('click');

                    // Hide elements that are supposed to remain unchecked
                    $("#salary_check_wrapper").hide();
                }
                else {
                    $("#salary_check_wrapper").show();
                }
            });
        });
    </script>
@append
