@extends('layouts.app')

@section('title', 'Overdue report')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2>Clients</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                    Reports
                </li>
                <li class="active">
                    <strong>Overdue Report</strong>
                </li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="wrapper wrapper-content">
        {{-- Overdue reasons --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Overdue reasons</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="project-list">
                            <b>Tasks without reason : <a href="{{ action('OverdueReasonController@reportWithoutReason') }}">{{ $total }}</a></b>
                            <hr>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Reasons</th>
                                        @foreach ($overdueReasons as $overdueReason)
                                            <th>{{$overdueReason->reason}}</th>
                                        @endforeach
                                        <th>Tasks without reason</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td class="project-title">
                                                <a href="{{ action('UserController@show', $user) }}">{{ $user->name }}</a>
                                                @if ($user->hasFlags())
                                                    @include('flag-user.flagged', ['color' => $user->flagColor()])
                                                @endif
                                            </td>
                                            @foreach ($overdueReasons as $overdueReason)
                                                @if(isset($overdues[$user->id]) && isset($overdues[$user->id][$overdueReason->id]))
                                                    <td>
                                                        <a style="@if($overdueReason->threshold_value && $overdueReason->threshold_value < $overdues[$user->id][$overdueReason->id]) color:red @endif" href="{{ action('OverdueReasonController@reportReason', $user) }}?reason={{ $overdueReason->id }}">{{ $overdues[$user->id][$overdueReason->id] }}</a>
                                                    </td>
                                                @else
                                                    <td>0</td>
                                                @endif
                                            @endforeach
                                            <td>
                                                @if(isset($overdues[$user->id]) && isset($overdues[$user->id][0]))
                                                    <a href="{{ action('OverdueReasonController@reportWithoutReason', ['user'=>$user->id]) }}">{{ $overdues[$user->id][0] }}</a>
                                                @else
                                                    0
                                                @endif
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