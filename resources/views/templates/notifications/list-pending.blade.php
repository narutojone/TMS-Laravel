@extends('layouts.app')

@section('title', 'View template notifications list')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2>View notification</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li class="active">
                    <strong>Pending notifications</strong>
                </li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="wrapper wrapper-content">

                <div class="ibox">
                    <div class="ibox-title">
                        <h5>All pending notification templates</h5>
                    </div>
                    @if ($processedNotifications->count() > 0)
                        <form method="POST">
                            {{ csrf_field() }}
                            <div class="ibox-content">
                                <button class="btn btn-primary btn-sm" type="submit" formaction="{{ action('ProcessedTemplatesController@approve') }}">Approve all selected</button>
                                <button class="btn btn-warning btn-sm" type="submit" formaction="{{ action('ProcessedTemplatesController@decline') }}">Decline all selected</button>
                            </div>
                            <div class="ibox-content">
                                <div class="table-responsive">
                                    @include('templates.notifications.list-notifications-table', ['processedNotifications' => $processedNotifications])
                                    <div class="text-center">
                                        {{ $processedNotifications->links() }}
                                    </div>
                                </div>
                            </div>
                            <div class="ibox-content">
                                <button class="btn btn-primary btn-sm" type="submit" formaction="{{ action('ProcessedTemplatesController@approve') }}">Approve all selected</button>
                                <button class="btn btn-warning btn-sm" type="submit" formaction="{{ action('ProcessedTemplatesController@decline') }}">Decline all selected</button>
                            </div>
                        </form>
                    @else
                        <div class="ibox-content">
                            <i class="text-muted">no notifications template</i>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        $('#select-all').on('click', function(){
            if ($(this).is(":checked")) {
                $('.i-checks').prop('checked', true);
                $('#label-text').text('Deselect all');
            } else {
                $('.i-checks').prop('checked', false);
                $('#label-text').text('Select all');
            }
        });
    </script>
@stop