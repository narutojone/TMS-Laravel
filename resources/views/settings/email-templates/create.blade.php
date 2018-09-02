@extends('layouts.app')

@section('title', 'Create Email Template')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2>Email Templates</h2>
            <ol class="breadcrumb">
                <li><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('email_templates.index') }}">Email Templates</a></li>
                <li class="active"><strong>Create</strong></li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="wrapper wrapper-content animated fadeInUp">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Add New Email Template</h5>
                    </div>
                    <div class="ibox-content">
                        <form id="create-email-template-form" class="form-horizontal" method="POST" action="{{ route('email_templates.store') }}">
                            {{ csrf_field() }}

                            @include('partials.forms.input', [
                                'name' => 'name',
                                'label' => 'Template Name'
                            ])

                            @include('partials.forms.select', [
                                'name' => 'template_file',
                                'label' => 'Template View',
                                'options' => $emailLayouts
                            ])

                            <div class="hr-line-dashed"></div>

                            @include('partials.forms.input', [
                                'name' => 'title',
                                'label' => 'Body Title (14 Char)'
                            ])

                            @include('partials.forms.quillContent', [
                                'id' => 'editor-1',
                                'name' => 'content',
                                'form' => '#create-email-template-form',
                                'label' => 'Body Content'
                            ])

                            @include('partials.forms.quillContent', [
                                'id' => 'editor-2',
                                'name' => 'footer',
                                'form' => '#create-email-template-form',
                                'label' => 'Body Footer'
                            ])

                            <div class="form-group">
                                <input type="hidden" id="folder_id" name="folder_id" value="{{ $folderId }}" />
                                <label class="col-sm-2 control-label" for="active">Active</label>
                                <div class="col-sm-10">
                                    <input type="hidden" name="active" value="0" />
                                    <input type="checkbox" class="js-switch" id="active" value="1" name="active" {{ (old('active', 1)) ? 'checked' : ''}}>
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