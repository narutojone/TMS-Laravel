@extends('layouts.app')

@section('title', 'View template notification')

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>View notification</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url('/dashboard') }}">Dashboard</a>
            </li>
            <li>
                <a href="{{ action('TemplateController@show', $template) }}">{{ $template->title }}</a>
            </li>
            <li class="active">
                <strong>View notification</strong>
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
                    <h5>View notification</h5>
                </div>
                <div class="ibox-content">
                    <form id="form" class="form-horizontal">
                        <input id="notificationVars" value="{{ route('templates.notifications.variables', $notification) }}" type="hidden">

                        {{-- Type --}}
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="title">Type</label>

                            <div class="col-sm-10">
                                <select name="type" id="type" class="form-control chosen-select" required autofocus disabled>
                                    @foreach(array_keys(config('tms.notifiers')) as $type)
                                        <option value="{{ $type }}" {{ old('type', $notification->type) == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        {{-- User Type --}}
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="user_type">User Type</label>

                            <div class="col-sm-10">
                                <select name="user_type" id="user_type" class="form-control chosen-select" required autofocus disabled>
                                    @foreach(\App\Repositories\TemplateNotification\TemplateNotification::$userTypes as $value => $label)
                                        <option value="{{ $value }}" {{ old('user_type', $notification->user_type) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        {{-- Template --}}
                        <div class="form-group" v-show="form.type == 'template'">
                            <label class="col-sm-2 control-label" for="title">Template</label>

                            <div class="col-sm-10">
                                <select name="template" id="template" class="form-control chosen-select" disabled>
                                    <option value="">Select template ...</option>
                                    @foreach($emailTemplates as $template)
                                        <option value="{{ $template->id }}" {{ old('template', $notification->details['template'] ?? '') == $template->id ? 'selected' : '' }}>{{ $template->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Subject --}}
                        <div class="form-group" v-show="form.type != 'sms'">
                            <label class="col-sm-2 control-label" for="subject">Subject</label>

                            <div class="col-sm-10">
                                <input disabled type="text" id="subject" name="subject" class="form-control" value="{{ old('subject', $notification->details['subject'] ?? '') }}">
                            </div>
                        </div>

                        {{-- Vars --}}
                        <div class="form-group" v-for="(variable, index) in form.vars" v-show="form.type == 'template'">
                            <label class="col-sm-2 control-label" v-text="varToLabel(variable, index)"></label>

                            <div class="col-sm-10">
                                <input disabled type="text" :name="'vars[' + variable + ']'" class="form-control" :value="vars[variable]">
                                <small>Dynamic variables: <strong>[[clientname]]</strong>, <strong>[[deadline]]</strong>, <strong>[[taskname]]</strong> and <strong>[[taskdeliveredlink]]</strong></small>
                            </div>
                        </div>

                        <div class="hr-line-dashed" v-if="form.type == 'template'"></div>

                        {{-- message --}}
                        <div class="form-group" v-show="form.type != 'template'">
                            <label class="col-sm-2 control-label" for="message">Message</label>

                            <div class="col-sm-10">
                                <textarea disabled name="message" id="message" class="form-control">{{ old('message', $notification->details['message'] ?? '') }}</textarea>
                                <small>Dynamic variables: <strong>[[clientname]]</strong>, <strong>[[deadline]]</strong>, <strong>[[taskname]]</strong> and <strong>[[taskdeliveredlink]]</strong></small>
                            </div>
                        </div>

                        <div class="hr-line-dashed" v-show="form.type != 'template'"></div>

                        {{-- beforeDeadline --}}
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="before">Before</label>

                            <div class="col-sm-10">
                                <input disabled type="number" step="1" name="before" id="before" class="form-control" value="{{ old('before', $notification->before) }}">
                            </div>
                        </div>

                        <div class="hr-line-dashed" v-show="form.type != 'template'"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="paid">Paid</label>
                            <div class="col-sm-10">
                                <select name="paid" id="paid" class="form-control chosen-select" required autofocus>
                                    @foreach(\App\Repositories\TemplateNotification\TemplateNotification::$statusesForSending as $value => $label)
                                        <option value="{{ $value }}" {{ $notification->paid == $value ? 'selected' : '' }}>{{ ucfirst($label) }}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('paid'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('paid') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="completed">Completed</label>
                            <div class="col-sm-10">
                                <select name="completed" id="completed" class="form-control chosen-select" required autofocus>
                                    @foreach(\App\Repositories\TemplateNotification\TemplateNotification::$statusesForSending as $value => $label)
                                        <option value="{{ $value }}" {{ $notification->completed == $value ? 'selected' : '' }}>{{ ucfirst($label) }}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('completed'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('completed') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="completed">Delivered</label>
                            <div class="col-sm-10">
                                <select name="delivered" id="delivered" class="form-control chosen-select" required autofocus>
                                    @foreach(\App\Repositories\TemplateNotification\TemplateNotification::$statusesForSending as $value => $label)
                                        <option value="{{ $value }}" {{ $notification->delivered == $value ? 'selected' : '' }}>{{ ucfirst($label) }}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('completed'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('completed') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="hr-line-dashed" v-show="form.type != 'template'"></div>
                    </form>

                    @if ($emailTemplate)
                        <div class="hr-line-dashed"></div>
                        <div>
                            <h3>Preview</h3>
                            <div class="hr-line-dashed"></div>
                            {!! $emailTemplate !!}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
    <script>
        new Vue({
            el: '#form',
            data: {
                notification: {},
                vars: [],
                form: {
                    type: null,
                    template: '',
                    vars: []
                }
            },
            methods: {
                getTemplateVars: function (id) {
                    var that = this;
                    axios.get('/email_templates/' + id).then(function (response) {
                        that.form.vars = response.data
                    })
                },
                varToLabel: function (variable, index) {
                    return 'Variable ' + ++index + ': ' + variable.charAt(0).toUpperCase() + variable.slice(1);
                }
            },
            mounted: function () {
                $('.chosen-select').chosen().change(function (e) {
                    this.form[e.target.name] = e.target.value

                    if (e.target.name == 'template') {
                        this.getTemplateVars(e.target.value)
                    }

                    $('.form-group').removeClass('has-error')
                    $('.help-block').remove()
                    $('.alert-danger').remove()
                }.bind(this));

                $('.chosen-container').css('width', '100%')
                this.form.type = $('#type').val()
                this.form.template = $('#template').val()

                if (this.form.template) {
                    this.getTemplateVars(this.form.template)

                    var that = this;
                    axios.get($('#notificationVars').val()).then(function (response) {
                        that.vars = response.data
                    })
                }
            }
        });
    </script>
@stop
