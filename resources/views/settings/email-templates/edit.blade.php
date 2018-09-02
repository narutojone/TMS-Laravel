@extends('layouts.app')

@section('title', 'Create Email Template')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2>Email Templates</h2>
            <ol class="breadcrumb">
                <li><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('email_templates.index') }}">Email Templates</a></li>
                <li class="active"><strong>Edit</strong></li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
@if(! $emailTemplate->active)
    <div class="row">
        <div class="col-lg-12">
            <div class="wrapper wrapper-content animated fadeInUp" style="padding-bottom: 0;">
                <div class="alert alert-danger m-b-none">
                    <strong>Email template is currently deactivated!</strong>
                </div>
            </div>
        </div>
    </div>
@endif
    <div class="row">
        <div class="col-lg-12">
            <div class="wrapper wrapper-content animated fadeInUp">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Edit Email Template</h5>
                    </div>
                    <div class="ibox-content">
                        <form id="create-email-template-form" class="form-horizontal" method="POST" action="{{ route('email_templates.update', $emailTemplate) }}">
                            {{ csrf_field() }}
                            {{ method_field('PATCH') }}

                            @include('partials.forms.input', [
                                'name' => 'name',
                                'label' => 'Template Name',
                                'value' => $emailTemplate->name
                            ])

                            @include('partials.forms.select', [
                                'name' => 'template_file',
                                'label' => 'Template View',
                                'options' => $emailLayouts,
                                'value' => $emailTemplate->template_file
                            ])

                            <div class="hr-line-dashed"></div>

                            @include('partials.forms.input', [
                                'name' => 'title',
                                'label' => 'Body Title (14 Char)',
                                'value' => $emailTemplate->title
                            ])

                            @include('partials.forms.quillContent', [
                                'id' => 'editor-1',
                                'name' => 'content',
                                'form' => '#create-email-template-form',
                                'label' => 'Body Content',
                                'value' => $emailTemplate->content
                            ])

                            @include('partials.forms.quillContent', [
                                'id' => 'editor-2',
                                'name' => 'footer',
                                'form' => '#create-email-template-form',
                                'label' => 'Body Footer',
                                'value' => $emailTemplate->footer
                            ])

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="active">Active</label>

                                <div class="col-sm-10">
                                    <input type="hidden" name="active" value="0" />
                                    <input type="checkbox" class="js-switch" id="active" value="1" name="active" {{ (old('active', $emailTemplate->active)) ? 'checked' : ''}}>
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>

                            <div class="form-group m-b-none">
                                <div class="col-sm-10 col-sm-offset-2 m-b-lg">
                                    <small>The following global dynamic variables are available: <strong>[[clientname]]</strong>,<strong>[[employeename]]</strong> and <strong>[[employeepf]]</strong></small>
                                </div>
                                <div class="col-sm-4 col-sm-offset-2">
                                    <a class="btn btn-white" href="{{ route('email_templates.index') }}">Cancel</a>
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