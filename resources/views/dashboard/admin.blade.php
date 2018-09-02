@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="wrapper wrapper-content">

    {{-- Stats --}}
    <div class="row">
        <div class="col-lg-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-success pull-right">Today</span>
                    <h5>Tasks completed</h5>
                </div>
                <div class="ibox-content text-center">
                    <h1 class="no-margins">{{ App\Repositories\Task\Task::completed()->whereDate('completed_at', Carbon\Carbon::now()->format('Y-m-d'))->count() }}</h1>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-info pull-right">Overall</span>
                    <h5>Open tasks</h5>
                </div>
                <div class="ibox-content text-center">
                    <h1 class="no-margins">{{ App\Repositories\Task\Task::uncompleted()->where('active',1)->count() }}</h1>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-warning pull-right">Today</span>
                    <h5>Task due</h5>
                </div>
                <div class="ibox-content text-center">
                    <h1 class="no-margins">{{ App\Repositories\Task\Task::uncompleted()->where('active',1)->whereDate('deadline', Carbon\Carbon::now()->format('Y-m-d'))->count() }}</h1>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-danger pull-right">Ovedue</span>
                    <h5>Tasks overdue</h5>
                </div>
                <div class="ibox-content text-center">
                    <h1 class="no-margins">{{ App\Repositories\Task\Task::overdue()->where('active',1)->count() }}</h1>
                </div>
            </div>
        </div>
    </div>

    {{-- Overdue tasks --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Overdue tasks</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-3">
                            <form role="form" method="get" action="">
                                {{-- User --}}
                                <div class="form-group">
                                    <label class="control-label" for="user">User</label>

                                    <select class="form-control chosen-select" name="user" id="user">
                                        <option></option>
                                        @foreach ($users as $user)
                                            <option{{ ($selectedUser == $user->id) ? ' selected' : '' }} value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Client --}}
                                <div class="form-group">
                                    <label class="control-label m-r-xs" for="client">Client</label>

                                    <select class="form-control chosen-select" name="client" id="client">
                                        <option></option>
                                        @foreach ($clients as $client)
                                            <option{{ ($selectedClient == $client->id) ? ' selected' : '' }} value="{{ $client->id }}">{{ $client->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Category --}}
                                <div class="form-group">
                                    <label class="control-label m-r-xs" for="category">Category</label>

                                    <select class="form-control chosen-select" name="category" id="category">
                                        <option></option>
                                        @foreach ($categories as $category)
                                            <option{{ ($selectedCategory == $category) ? ' selected' : '' }}>{{ $category }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Overdue reason --}}
                                <div class="form-group">
                                    <label class="control-label m-r-xs" for="reason">Overdue reason</label>

                                    <select class="form-control chosen-select" name="reason" id="reason">
                                        <option></option>
                                        <option value="no_reason" {{ ($selectedOverdue === 'no_reason') ? ' selected' : '' }}>No reason</option>
                                        @foreach ($overdues as $overdue)
                                            <option{{ ($selectedOverdue == $overdue->id) ? ' selected' : '' }} value="{{ $overdue->id }}">{{ $overdue->reason }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <button class="btn btn-primary" type="submit">Filter</button>
                            </form>

                            <div class="hr-line-dashed hidden-md hidden-lg"></div>
                        </div>
                        <div class="col-md-9">
                            @if ($tasks->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover issue-tracker m-b-none">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Client</th>
                                                <th>User</th>
                                                <th>Deadline</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($tasks as $task)
                                                <tr>
                                                    <td class="issue-info">
                                                        <a href="{{ action('TaskController@show', $task) }}">{{ $task->title }}</a>
                                                        <small>{{ $task->category }}</small>
                                                    </td>
                                                    <td>
                                                        <a href="{{ action('ClientController@show', $task->client) }}">{{ $task->client->name }}</a>
                                                    </td>
                                                    <td style="width: 35%;">
                                                        @if ($task->user)
                                                            <a href="{{ action('UserController@show', $task->user) }}">{{ $task->user->name }}</a>
                                                            @if($task->user->out_of_office)
                                                                <span class="label label-danger m-l-md">Out of office</span>
                                                            @endif
                                                        @else
                                                            <i class="text-muted">no user</i>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="label label-danger" style="{{ $task->isOverdue() && $task->overdueReason ? 'background-color:' . $task->overdueReason->overdueReason->hex : '' }}">@date($task->deadline)</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                    {{-- Pagination links --}}
                                    <div class="text-center">
                                        {{ $tasks->links() }}
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
    </div>

    <div class="row">

        {{-- Clients without manager --}}
        <div class="col-lg-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Clients without manager</h5>
                </div>
                <div class="ibox-content">
                    @if ($clientsWithoutManager->count() > 0)
                        <div class="project-list">
                            <table class="table table-hover">
                                <tbody>
                                    @foreach ($clientsWithoutManager as $client)
                                        <tr>
                                            <td class="project-title">
                                                <a href="{{ action('ClientController@show', $client->id) }}">{{ $client->name }}</a>
                                                <br>
                                                @if ($client->system)
                                                    <small>{{ $client->system->name }}</small>
                                                @endif
                                            </td>
                                            <td class="project-actions">
                                                <a href="{{ action('ClientController@show', $client->id) }}" class="btn btn-white btn-sm">
                                                    <i class="fa fa-folder"></i> View
                                                </a>
                                                @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
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
                    @else
                        <i class="text-muted">no clients without manager</i>
                    @endif
                </div>
            </div>
        </div>

        {{-- Clients without employee --}}
        <div class="col-lg-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Clients without employee</h5>
                </div>
                <div class="ibox-content">
                    @if ($clientsWithoutEmployee->count() > 0)
                        <div class="project-list">
                            <table class="table table-hover">
                                <tbody>
                                    @foreach ($clientsWithoutEmployee as $client)
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
                    @else
                        <i class="text-muted">no clients without employee</i>
                    @endif
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
                    @if (App\Repositories\Task\Task::whereNull('user_id')->active()->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover issue-tracker m-b-none">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Client</th>
                                        <th>Deadline</th>
                                        <th>Due</th>
                                        <th>Repeat frequency</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (App\Repositories\Task\Task::whereNull('user_id')->active()->with('client')->orderBy('due_at')->get() as $task)
                                        <tr>
                                            <td class="issue-info">
                                                <a href="{{ action('TaskController@show', $task) }}">{{ $task->title }}</a>
                                                <small>{{ $task->category }}</small>
                                            </td>
                                            @if ($task->client)
                                            <td>
                                                <a href="{{ action('ClientController@show', $task->client) }}">{{ $task->client->name }}</a>
                                            </td>
                                            @endif
                                            <td>
                                                <span class="label label-{{ $task->deadlineClass() }}">@date($task->deadline)</span>
                                            </td>
                                            <td>
                                                <span class="label label-{{ $task->dueDateCountDown()['class'] }}">{{ $task->dueDateCountDown()['label'] }}</span>
                                            </td>
                                            <td>
                                                @if ($task->repeating)
                                                    @frequency($task->frequency)
                                                @else
                                                    <i class="text-muted">not repeating</i>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <i class="text-muted">no unassigned tasks</i>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
