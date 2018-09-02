@extends('layouts.app')

@section('title', 'Create template')

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Create notification</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url('/dashboard') }}">Dashboard</a>
            </li>
            <li>
                <a href="{{ action('TemplateController@show', $template) }}">{{ $template->title }}</a>
            </li>
            <li class="active">
                <strong>Create notification</strong>
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
                    <h5>Create notification <small>Add a new notification to the system for template {{ $template->title }}.</small></h5>
                </div>
                <div class="ibox-content">
                    <form id="form" class="form-horizontal" role="form" method="POST" action="{{ route('templates.notifications.store', $template) }}">
                        {{ csrf_field() }}

                        {{-- Type --}}
                        <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="title">Type</label>

                            <div class="col-sm-10">
                                <select name="type" id="type" class="form-control chosen-select" required autofocus>
                                    @foreach(array_keys(config('tms.notifiers')) as $type)
                                        <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('type'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('type') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        {{-- User Type --}}
                        <div class="form-group{{ $errors->has('user_type') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="user_type">User Type</label>

                            <div class="col-sm-10">
                                <select name="user_type" id="user_type" class="form-control chosen-select" required autofocus>
                                    @foreach(\App\Repositories\TemplateNotification\TemplateNotification::$userTypes as $value => $label)
                                        <option value="{{ $value }}" {{ old('user_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('user_type'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('user_type') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        {{-- Template --}}
                        <div class="form-group{{ $errors->has('template') ? ' has-error' : '' }}" v-show="form.type == 'template'">
                            <label class="col-sm-2 control-label" for="title">Template</label>

                            <div class="col-sm-10">
                                <select name="template" id="template" class="form-control chosen-select">
                                    <option value="">Select template ...</option>
                                    @foreach($emailTemplates as $template)
                                        <option value="{{ $template->id }}" {{ old('template') == $template->id ? 'selected' : '' }}>{{ $template->name }}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('template'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('template') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Subject --}}
                        <div class="form-group{{ $errors->has('subject') ? ' has-error' : '' }}" v-show="form.type != 'sms'">
                            <label class="col-sm-2 control-label" for="subject">Subject</label>

                            <div class="col-sm-10">
                                <input type="text" id="subject" name="subject" class="form-control" value="{{ old('subject') }}">

                                @if ($errors->has('subject'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('subject') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Vars --}}
                        <div class="form-group{{ $errors->has('vars') ? ' has-error' : '' }}" v-for="(variable, index) in form.vars" v-show="form.type == 'template'">
                            <label class="col-sm-2 control-label" v-text="varToLabel(variable, index)"></label>

                            <div class="col-sm-10">
                                <input type="text" :name="'vars[' + variable + ']'" class="form-control">

                                @if ($errors->has('vars'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('vars') }}</strong>
                                    </span>
                                @endif
                                <small>Dynamic variables: <strong>[[clientname]]</strong>,<strong>[[employeename]]</strong>,<strong>[[employeepf]]</strong>, <strong>[[deadline]]</strong>, <strong>[[taskname]]</strong> and <strong>[[taskdeliveredlink]]</strong></small>
                            </div>
                        </div>

                        <div class="hr-line-dashed" v-if="form.type == 'template'"></div>

                        {{-- message --}}
                        <div class="form-group{{ $errors->has('message') ? ' has-error' : '' }}" v-show="form.type != 'template'">
                            <label class="col-sm-2 control-label" for="message">Message</label>

                            <div class="col-sm-10">
                                <textarea name="message" id="message" class="form-control">{{ old('message') }}</textarea>

                                @if ($errors->has('message'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('message') }}</strong>
                                    </span>
                                @endif
                                <small>Dynamic variables: <strong>[[clientname]]</strong>,<strong>[[employeename]]</strong>,<strong>[[employeepf]]</strong>, <strong>[[deadline]]</strong>, <strong>[[taskname]]</strong> and <strong>[[taskdeliveredlink]]</strong></small>
                            </div>
                        </div>

                        <div class="hr-line-dashed" v-show="form.type != 'template'"></div>

                        {{-- beforeDeadline --}}
                        <div class="form-group{{ $errors->has('before') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="before">Before</label>

                            <div class="col-sm-10">
                                <input type="number" step="1" name="before" id="before" class="form-control" value="{{ old('before', 14) }}">

                                @if ($errors->has('before'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('before') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="hr-line-dashed" v-show="form.type != 'template'"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="paid">Paid</label>
                            <div class="col-sm-10">
                                <select name="paid" id="paid" class="form-control chosen-select" required>
                                    @foreach(\App\Repositories\TemplateNotification\TemplateNotification::$statusesForSending as $value => $label)
                                        <option value="{{ $value }}" {{ old('paid', App\Repositories\TemplateNotification\TemplateNotification::TRUE) == $value ? 'selected' : '' }}>{{ ucfirst($label) }}</option>
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
                                <select name="completed" id="completed" class="form-control chosen-select" required>
                                    @foreach(\App\Repositories\TemplateNotification\TemplateNotification::$statusesForSending as $value => $label)
                                        <option value="{{ $value }}" {{ old('completed', \App\Repositories\TemplateNotification\TemplateNotification::FALSE) == $value ? 'selected' : '' }}>{{ ucfirst($label) }}</option>
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
                                <select name="delivered" id="delivered" class="form-control chosen-select" required>
                                    @foreach(\App\Repositories\TemplateNotification\TemplateNotification::$statusesForSending as $value => $label)
                                        <option value="{{ $value }}" {{ old('delivered', \App\Repositories\TemplateNotification\TemplateNotification::FALSE) == $value ? 'selected' : '' }}>{{ ucfirst($label) }}</option>
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
                            <label class="col-sm-2 control-label" for="paused">Paused</label>
                            <div class="col-sm-10">
                                <select name="paused" id="paused" class="form-control chosen-select" required>
                                    @foreach(\App\Repositories\TemplateNotification\TemplateNotification::$statusesForSending as $value => $label)
                                        <option value="{{ $value }}" {{ old('paused', \App\Repositories\TemplateNotification\TemplateNotification::FALSE) == $value ? 'selected' : '' }}>{{ ucfirst($label) }}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('paused'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('paused') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="hr-line-dashed" v-show="form.type != 'template'"></div>

                        <div class="form-group m-b-none">
                            <div class="col-sm-4 col-sm-offset-2">
                                <a class="btn btn-white" href="{{ action('TemplateController@index') }}">Cancel</a>
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

@section('javascript')
    <script>
        new Vue({
            el: '#form',
            data: {
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
                        console.log(this.form.vars)
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
                }
            }
        });
    </script>
@stop
