@extends('layouts.app')

@section('title', 'IT Report')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>IT Report</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                    Reports
                </li>
                <li>
                    <a href="{{ route('reports.it.github_issues') }}">IT Report</a>
                </li>
                <li class="active">
                    <strong>#{{ $githubIssue->issue_number }} : {{ $githubIssue->issue_title }}</strong>
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
                    <div class="ibox-content">
                        @if ($githubIssue->harvestTimeEntities->count() > 0)
                            <table class="table">
                                <thead>
                                <tr>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Tracked</th>
                                    <th>User</th>
                                    <th>Description</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($githubIssue->harvestTimeEntities as $timeEntity)
                                    <tr>
                                        <td class="text-center">{{ $timeEntity->spent_date }}</td>
                                        <td class="text-center">{{ $timeEntity->tracked_time }}</td>
                                        <td>{{ $timeEntity->username }}</td>
                                        <td>{{ $timeEntity->notes }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
