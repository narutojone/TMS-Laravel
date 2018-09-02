@extends('layouts.app')

@section('title', 'Overdue Reasons')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2>Overdue Reasons</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li class="active">
                    <strong>Overdue Reasons</strong>
                </li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="wrapper wrapper-content">

                {{-- Reasons --}}
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Overdue Reasons</h5>
                        <div class="ibox-tools">
                            <a href="{{ action('OverdueReasonController@create') }}" class="btn btn-primary btn-xs">Add new reason</a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="project-list">
                            <table class="table table-hover">
                                <tbody>
                                @foreach ($reasons as $reason)
                                    <tr>
                                        <td style="width: 60px;">
                                            <form role="form" method="POST" action="{{ action('OverdueReasonController@move', $reason) }}">
                                                {{ csrf_field() }}

                                                {{-- Up --}}
                                                @if ($reason->priority > 1)
                                                    <button type="submit" name="direction" value="up" class="btn btn-xs btn-default">&uarr;</button>
                                                @else
                                                    <button class="btn btn-xs btn-default" disabled>&uarr;</button>
                                                @endif

                                                {{-- Down --}}
                                                @if ($reason->priority < $reason->count())
                                                    <button type="submit" name="direction" value="down" class="btn btn-xs btn-default">&darr;</button>
                                                @else
                                                    <button class="btn btn-xs btn-default" disabled>&darr;</button>
                                                @endif
                                            </form>
                                        </td>
                                        <td class="project-title">
                                            <a href="{{ action('OverdueReasonController@edit', $reason) }}">{{ $reason->reason }}</a>
                                        </td>
                                        <td>{{$reason->days}} days</td>
                                        <td>{{ $reason->required ? 'Required' : ''}}</td>
                                        <td>
                                            <span style="background-color: {{ $reason->hex }};" class="label label-danger">Color</span>
                                        </td>
                                        <td>{{ $reason->visible ? 'Visible' : ''}}</td>
                                        <td>{{ $reason->is_visible_in_report ? 'Visible in report' : ''}}</td>
                                        <td>Threshold value: {{ (int) $reason->threshold_value }}</td>
                                        <td>
                                            @if( ! $reason->active)
                                                <span class="label label-danger">Deactivated</span>
                                            @endif
                                        <td class="project-actions">
                                            <a href="{{ action('OverdueReasonController@edit', $reason) }}" class="btn btn-white btn-sm"><i class="fa fa fa-pencil"></i> Edit</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection