@extends('layouts.app')

@section('title', 'Employee Dashboard')

@section('content')
    <style>
        .issue-info{
            width: 30%;
        }
    </style>
    @foreach($unreadNotifications as $task)
        <div class="row">
            <div class="col-lg-12">
                <div class="wrapper wrapper-content animated fadeInUp" style="padding-bottom: 0;">
                    <div class="alert alert-success m-b-none">
                        <strong>{{ $task->client->name }} has marked the task <a target="_blank"
                                                                                 href="{{ action('TaskController@show', $task) }}">{{ $task->title }}</a>
                            as delivered. </strong> It should now be
                        ready to be completed.
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-lg-12">
                                <h2 class="m-b-none">Tasks</h2>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        {{-- Filter tasks --}}
                        @include('dashboard.filter_tasks', [
                            'categories' => $categories,
                            'selectedCategory' => $selectedCategory,
                            'clients' => $clients,
                            'selectedClient' => $selectedClient,
                        ])
                        <div class="hr-line-dashed"></div>

                        {{-- Overdue tasks --}}
                        @if ($overdue->count() > 0)
                            <h3>Overdue</h3>

                            @include('dashboard.employee_tasks', ['tasks' => $overdue, 'severity' => 'danger'])

                            <div class="hr-line-dashed"></div>
                        @endif

                        {{-- Uncompleted tasks thas are marked as delivered --}}
                        @if ($delivered->count() > 0)
                            <div class="ibox" style="border-bottom: 0 !important;">
                                <a class="collapse-link">
                                    <div class="btn btn-primary btn-xs pull-right" style="margin-right: 10px;">Show all
                                        delivered tasks
                                    </div>
                                </a>
                                <h3>Delivered tasks
                                    <small> (The client has told us they have delivered everything needed for you to
                                        complete the task)
                                    </small>
                                </h3>
                                <div class="ibox-content" style="display: none; border-width: 0; padding: 0;">
                                    @include('dashboard.employee_tasks', ['tasks' => $delivered, 'severity' => 'info'])
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                        @endif

                        {{-- Tasks due today --}}
                        <div>
                            <h3>Due today</h3>

                            @if ($today->count() > 0)
                                @include('dashboard.employee_tasks', ['tasks' => $today, 'severity' => 'warning'])
                            @else
                                <i class="text-muted">no tasks due today</i>
                            @endif
                        </div>
                        <div class="hr-line-dashed"></div>

                        {{-- Tasks due tomorrow --}}
                        <div>
                            <h3>Due tomorrow</h3>

                            @if ($tomorrow->count() > 0)
                                @include('dashboard.employee_tasks', ['tasks' => $tomorrow, 'severity' => 'info'])
                            @else
                                <i class="text-muted">no tasks due tomorrow</i>
                            @endif
                        </div>
                        <div class="hr-line-dashed"></div>

                        {{-- Tasks due in the next 7 days --}}
                        <div>
                            <h3>Due in the next 7 days</h3>

                            @if ($week->count() > 0)
                                @include('dashboard.employee_tasks', ['tasks' => $week, 'severity' => 'info'])
                            @else
                                <i class="text-muted">no tasks due in 2 - 7 days</i>
                            @endif
                        </div>
                        <div class="hr-line-dashed"></div>

                        {{-- Task due in the future --}}
                        <div>
                            <h3>Due in the future</h3>

                            @if ($future->count() > 0)
                                @include('dashboard.employee_tasks', ['tasks' => $future, 'severity' => 'info'])
                            @else
                                <i class="text-muted">no tasks due in 7+ days</i>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        var columnsToAdjust = [
            'reason-td'
        ];
        $(document).ready(function () {
            adjustTableColumnWidths();
        });

        function adjustTableColumnWidths() {
            columnsToAdjust.forEach(adjustTableSingleColumnWidths);
        }

        function adjustTableSingleColumnWidths(value) {
            var column = $('.' + value);
            var widthsArray = column.map(function () {
                return $(this).outerWidth();
            }).get();
            var elementMaxWidth = Math.max.apply(Math, widthsArray);
            column.css('width', elementMaxWidth);
        }
    </script>
@endsection