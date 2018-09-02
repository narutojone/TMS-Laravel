@extends('layouts.app')
@section('title', 'View notification')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2>View notification</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                    <a href="{{ url('/system_settings/templates/notifications/pending') }}">Unapproved notifications</a>
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
                    <form id="form" class="form-horizontal" method="POST">
                        {{ csrf_field() }}
                        <div class="ibox-title">
                            <h5>View notification</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="form-group">
                                @if (!($processedNotification['status'] == \App\Repositories\ProcessedNotification\ProcessedNotification::STATUS_APPROVED))
                                    <button type="submit" class="btn btn-primary btn-sm" formaction="{{ action('ProcessedTemplatesController@approve') }}">Approve notification</button>
                                @endif
                                @if (!($processedNotification['status'] == \App\Repositories\ProcessedNotification\ProcessedNotification::STATUS_DECLINED))
                                    <button type="submit" class="btn btn-warning btn-sm" formaction="{{ action('ProcessedTemplatesController@decline') }}">Decline notification</button>
                                @endif
                                <input type="hidden" name="processedNotificationIds[]" value="{{ $processedNotification['id'] }}">
                            </div>
                            @if ($processedNotification['template_notification']['type'] == \App\Repositories\TemplateNotification\TemplateNotification::TYPE_EMAIL)
                                @include('processedtemplates.show-email', ['processedNotification' => $processedNotification])
                            @elseif ($processedNotification['template_notification']['type'] == \App\Repositories\TemplateNotification\TemplateNotification::TYPE_SMS)
                                @include('processedtemplates.show-sms', ['processedNotification' => $processedNotification])
                            @elseif ($processedNotification['template_notification']['type'] == \App\Repositories\TemplateNotification\TemplateNotification::TYPE_TEMPLATE)
                                @include('processedtemplates.show-template', ['processedNotification' => $processedNotification])
                            @elseif ($processedNotification['type'] == \App\Repositories\ProcessedNotification\ProcessedNotification::CLIENT_UPDATE_EMAIL)
                                @include('processedtemplates.show-email-from-update-client', ['processedNotification' => $processedNotification])
                            @else
                                @include('processedtemplates.show-simple-sms', ['processedNotification' => $processedNotification])
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection