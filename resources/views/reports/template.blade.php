@extends('layouts.app')

@section('title', 'Reports')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2>Reports</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                    Reports
                </li>
                <li class="active">
                    <strong>{{ $report->name }}</strong>
                </li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="wrapper wrapper-content">

        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ $report->name }}</h5>
                    </div>
                    <div class="ibox-content">
                        @yield('report-content')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection