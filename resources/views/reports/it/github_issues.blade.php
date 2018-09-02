@extends('layouts.app')

@section('title', 'IT Report')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-6">
            <h2>IT Report</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                    Reports
                </li>
                <li>
                    IT Report
                </li>
                <li class="active">
                    <strong>GitHub Issues list</strong>
                    <span class="m-l label label-{{ ($hitRate < 90 || $hitRate > 110) ? 'danger' : 'primary'}}">Hit Rate: {{ $hitRate }}%</span>
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
                        <div class="row">
                            <div class="col-md-6">
                                <form role="form" class="form-inline">
                                    <div class="form-group">
                                        <form method="get" action="{{ route('reports.it.github_issues') }}">
                                            <label class="control-label m-r-xs" for="milestone_id">Sprint</label>
                                            <select  onchange="this.form.submit()" name="milestone_id" class="form-control">
                                                <option value="">All</option>
                                                @foreach($milestonesList as $milestone)
                                                    <option value="{{ $milestone->id }}" {{ ($milestoneId == $milestone->id) ? ' selected' : '' }}>{{ $milestone->title }}</option>
                                                @endforeach
                                            </select>
                                            <label class="control-label m-l-md m-r-xs" for="search">Issue State</label>
                                            <select  onchange="this.form.submit()" name="state" class="form-control">
                                                <option value="">All</option>
                                                <option value="open" {{ (request('state') == 'open') ? ' selected' : '' }}>Open</option>
                                                <option value="closed" {{ (request('state') == 'closed') ? ' selected' : '' }}>Closed</option>
                                            </select>
                                            <label class="control-label m-l-md m-r-xs" for="search">Issue Type</label>
                                            <select  onchange="this.form.submit()" name="type" class="form-control">
                                                <option value="0" {{ (request('type') == '0') ? ' selected' : '' }}>Regular</option>
                                                <option value="1" {{ (request('type') == '1') ? ' selected' : '' }}>Pull Request</option>
                                            </select>
                                        </form>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ route('reports.it.unmatched_time') }}" class="btn btn-primary btn-xs pull-right">Unmatched time</a>
                            </div>
                        </div>

                        @if ($issues->count() > 0)
                            <table class="table">
                                <thead>
                                <tr>
                                    <th class="text-center">Issue #</th>
                                    <th class="text-center">Milestone</th>
                                    <th class="text-center">Estimate</th>
                                    <th class="text-center">Tracked</th>
                                    <th class="text-center">Hit Rate</th>
                                    <th class="text-center">State</th>
                                    <th>Title</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($issues as $issue)
                                    <tr>
                                        <td class="text-center"><a href="{{ $issue->origin_url }}" target="_blank">#{{ $issue->issue_number }}</a></td>
                                        <td class="text-center">{{ $issue->milestone_title }}</td>
                                        <td class="text-center">{{ $issue->issue_estimate }}</td>
                                        <td class="text-center">
                                            @if ($issue->tracked > 0)
                                                <a href="{{ route('it.githubIssues.time_entries', ['issue'=>$issue->id]) }}" title="Show detailed log">{{ $issue->tracked }}</a>
                                            @else
                                                <span class="text-muted">0</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($issue->tracked)
                                                <span class="m-l label label-{{ ($issue->hitRate < 90 || $issue->hitRate > 110) ? 'danger' : 'primary'}}">
                                                    {{ $issue->hitRate }}%
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ ucfirst($issue->state) }}</td>
                                        <td>{{ $issue->issue_title }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif

                        <div class="text-center">
                            @if (is_null($milestoneId))
                                {{ $issues->appends(['milestone_id' => $milestoneId])->links() }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
