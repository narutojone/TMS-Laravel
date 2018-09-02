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
                    <a href="{{ route('reports.it.github_issues') }}">IT Report</a>
                </li>
                <li class="active">
                    <strong>Assign time entity</strong>
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
                                <form method="post" action="{{ route('it.timeEntity.assign', ['id' => $timeEntity->id]) }}">
                                    {{ csrf_field() }}
                                    <table class="table">
                                        <tbody>
                                        <tr>
                                            <td class="text-right">ID:</td>
                                            <td>{{ $timeEntity->id }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-right">Tracked time:</td>
                                            <td>{{ $timeEntity->tracked_time }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-right">Notes:</td>
                                            <td>{!! $timeEntity->notes !!}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-right">GitHub Issue:</td>
                                            <td>
                                                <input type="text"
                                                       class="form-control"
                                                       id="github-issue"
                                                       data-provide="typeahead"
                                                       placeholder="Search by issue number or title"
                                                       data-source='{{ $autoCompleteSource }}'
                                                       value="{{ $timeEntity->github_issue }}"
                                                       autocomplete="off"
                                                       required>
                                                <input type="hidden" id="github-issue-id" name="github-issue-id" value="">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>
                                                <button type="submit" class="btn btn-primary">Assign</button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script type="text/javascript">

        var $input = $("#github-issue");
        $input.change(function () {
            var current = $input.typeahead("getActive");
            if (current) {
                $('#github-issue-id').val(current.id);
            }
        });

    </script>
@endsection
