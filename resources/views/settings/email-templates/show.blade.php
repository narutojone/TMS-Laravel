@extends('layouts.app')

@section('title', 'Email Templates')

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Email Templates</h2>
        <ol class="breadcrumb">
            <li><a href="{{ url('/dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('email_templates.index') }}">Email Templates</a></li>
            <li class="active"><strong>{{ $emailTemplate->title }}</strong></li>
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
            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{ $emailTemplate->title }} - preview</h5>
                    <div class="ibox-tools"></div>
                </div>
                <div class="ibox-content">
                    <div class="project-list">
                        @include('layouts.emails.' . $emailTemplate->template_file, [
                            'title'   => $emailTemplate->title,
                            'content' => $emailTemplate->content_html,
                            'footer'  => $emailTemplate->footer_html,
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

