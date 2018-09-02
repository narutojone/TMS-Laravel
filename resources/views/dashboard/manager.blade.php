@extends('layouts.app')

@section('title', 'Manager Dashboard')

@section('content')
<style>
    .issue-info{
        width: 30%;
    }
</style>
<div class="wrapper wrapper-content">

    {{-- Clients without employee --}}
    @if (App\Repositories\Client\Client::whereNull('employee_id')->where('manager_id', Auth::user()->id)->count() > 0)
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Clients without employee</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="project-list">
                            <table class="table table-hover">
                                <tbody>
                                    @foreach (App\Repositories\Client\Client::whereNull('employee_id')->where('manager_id', Auth::user()->id)->get() as $client)
                                        <tr>
                                            <td class="project-title">
                                                <a href="{{ action('ClientController@show', $client->id) }}">{{ $client->name }}</a>
                                                <br>
                                                <small>{{ $client->industry }}</small>
                                            </td>
                                            <td class="project-actions">
                                                <a href="{{ action('ClientController@show', $client->id) }}" class="btn btn-white btn-sm">
                                                    <i class="fa fa-folder"></i> View
                                                </a>
                                                @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN) || Auth::user()->id == $client->manager_id)
                                                    <a href="{{ action('ClientController@edit', $client->id) }}" class="btn btn-white btn-sm">
                                                        <i class="fa fa-pencil"></i> Edit
                                                    </a>
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
    @endif

    {{-- Filter tasks --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Filter tasks</h5>
                </div>
                <div class="ibox-content">
                    {{-- Filter tasks --}}
                    @include('dashboard.filter_tasks', [
                        'categories' => $categories,
                        'selectedCategory' => $selectedCategory,
                        'clients' => $clients,
                        'selectedClient' => $selectedClient,
                    ])
                </div>
            </div>
        </div>
    </div>

    {{-- Unassigned tasks --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox" id="unassigned-tasks">
                <div class="ibox-title">
                    <h5>Unassigned tasks</h5>
                </div>
                <div class="ibox-content">
                    @if ($unassigned->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover issue-tracker m-b-none">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>User</th>
                                        <th>Client</th>
                                        <th>Deadline</th>
                                        <th>Due</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($unassigned as $task)
                                        <tr>
                                            <td class="issue-info">
                                                <a href="{{ action('TaskController@show', $task) }}">{{ $task->title }}</a>
                                                <small>{{ $task->category }}</small>
                                            </td>
                                            <td>
                                                <i class="text-muted">no user</i></td>
                                            <td  class="client-td" >
                                                <a href="{{ action('ClientController@show', $task->client) }}">{{ $task->client->name }}</a>
                                            </td>
                                            <td style="width:130px">
                                                <span class="label label-{{ $task->deadlineClass() }}">@date($task->deadline)</span>
                                            </td>
                                            <td style="width:100px">
                                                <span class="label label-{{ $task->dueDateCountDown()['class'] }}">{{ $task->dueDateCountDown()['label'] }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            {{-- Pagination links --}}
                            <div class="text-center">
                                {{ $unassigned->links() }}
                            </div>
                        </div>
                    @else
                        <i class="text-muted">no unassigned tasks</i>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Overdue tasks --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox" id="unassigned-tasks">
                <div class="ibox-title">
                    <h5>Overdue tasks</h5>
                </div>
                <div class="ibox-content">
                    @if ($overdue->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover issue-tracker m-b-none">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>User</th>
                                        <th>Client</th>
                                        <th>Deadline</th>
                                        <th>Due</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($overdue as $task)
                                        <tr>
                                            <td class="issue-info">
                                                <a href="{{ action('TaskController@show', $task) }}">{{ $task->title }}</a>
                                                @if (! $task->client->paid)
                                                    <span class="label label-danger m-l-md">Client has not paid</span>
                                                @endif
                                                <small>{{ $task->category }}</small>
                                            </td>
                                            <td>
                                                @if ($task->user)
                                                    @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                                                        <a href="{{ action('UserController@show', $task->user) }}">{{ $task->user->name }}</a>
                                                    @else
                                                        {{ $task->user->name }}
                                                    @endif
                                                    @if($task->user->out_of_office)
                                                        <span class="label label-danger m-l-md">Out of office</span>
                                                    @endif
                                                @else
                                                    <i class="text-muted">no user</i>
                                                @endif
                                            </td>
                                            <td class="client-td" >
                                                <a href="{{ action('ClientController@show', $task->client) }}">{{ $task->client->name }}</a>
                                            </td>
                                            <td style="width:130px">
                                                <span class="label label-{{ $task->deadlineClass() }}" style="{{ $task->taskOverdueReasons()->orderBy('created_at', 'DESC')->first() ? 'background-color:' . $task->taskOverdueReasons()->orderBy('created_at', 'DESC')->first()->overdueReason->hex : '' }}">@date($task->deadline)</span>
                                            </td>
                                            <td style="width:100px">
                                                <span class="label label-{{ $task->dueDateCountDown()['class'] }}">{{ $task->dueDateCountDown()['label'] }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            {{-- Pagination links --}}
                            <div class="text-center">
                                {{ $overdue->links() }}
                            </div>
                        </div>
                    @else
                        <i class="text-muted">no overdue tasks</i>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script type="text/javascript">
        var columnsToAdjust = [
            'client-td'
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