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
                    <strong>Unmatched time entities list</strong>
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

                        @if (session('message'))
                            <div class="alert alert-success">
                                {{ session('message') }}
                            </div>
                        @endif

                        @if ($timeEntries->count() > 0)
                            <table class="table">
                                <thead>
                                <tr>
                                    <th class="text-center">Tracked Time</th>
                                    <th>Username</th>
                                    <th>Notes</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($timeEntries as $timeEntry)
                                    <tr>
                                        <td class="text-center">{{ $timeEntry->tracked_time }}</td>
                                        <td>{{ $timeEntry->username }}</td>
                                        <td>{!! $timeEntry->notes !!}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button data-toggle="dropdown" class="btn btn-default btn-xs dropdown-toggle" aria-expanded="true">Action <span class="caret"></span></button>
                                                <ul class="dropdown-menu">
                                                    <li><a href="{{ route('it.timeEntity.assign.form', ['id' => $timeEntry->id]) }}">Assign</a></li>
                                                    <li><a class="disregard-time-entry" href="{{ route('it.timeEntity.disregard', ['id' => $timeEntry->id]) }}">To disregard</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif

                        <div class="text-center">
                            {{ $timeEntries->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script type="text/javascript">

        $(document).on('click', 'a.disregard-time-entry', function (e) {
            e.preventDefault();
            var self = $(this);
            $.ajax({
                method: "POST",
                url: self.attr('href'),
                data: {_token: "{{csrf_token()}}"}
            })
                .done(function (response) {
                    if (response) {
                        self.closest('tr').remove();
                    }
                })
                .error(function () {
                    swal('Error!', 'Internal error', 'error');
                });
        });
    </script>
@endsection