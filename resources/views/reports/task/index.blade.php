@extends('layouts.app')

@section('title', 'Uncompleted tasks')

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
                <strong>Task Report</strong>
            </li>
        </ol>
    </div>
</div>
@endsection

@section('content')
@if($selectedViewAsUser)
    <div class="row">
        <div class="col-lg-12">
            <div class="wrapper wrapper-content animated fadeInUp" style="padding-bottom: 0;">
                <div class="alert alert-danger m-b-none">
                    <strong>You are currently viewing the report as another user</strong>
                </div>
            </div>
        </div>
    </div>
@endif
<div class="wrapper wrapper-content">

    {{-- Tasks --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{ $taskcount }} tasks found</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-3">
                            <form role="form" method="get" action="">
                                {{-- Completed --}}
                                <div class="form-group">
                                    <label class="control-label" for="completed">Completed</label>

                                    <select class="form-control chosen-select" name="completed" id="completed">
                                        <option></option>
                                        @foreach ($completed as $complete)
                                            <option{{ ($selectedCompleted == $complete) ? ' selected' : '' }}>{{ $complete }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Delivered --}}
                                <div class="form-group">
                                    <label class="control-label" for="delivered">Delivered</label>

                                    <select class="form-control chosen-select" name="delivered" id="delivered">
                                        <option></option>
                                        @foreach ($delivered as $deliver)
                                            <option{{ ($selectedDelivered == $deliver) ? ' selected' : '' }}>{{ $deliver }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Paid --}}
                                <div class="form-group">
                                    <label class="control-label" for="paid">Paid</label>

                                    <select class="form-control chosen-select" name="paid" id="paid">
                                        <option></option>
                                        @foreach ($paid as $pay)
                                            <option{{ ($selectedPaid == $pay) ? ' selected' : '' }}>{{ $pay }}</option>
                                        @endforeach
                                    </select>
                                </div>

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

                                {{-- View as user --}}
                                <div class="form-group">
                                    <label class="control-label" for="viewasuser">View report as another user</label>

                                    <select class="form-control chosen-select" name="viewasuser" id="viewasuser">
                                        <option></option>
                                        @foreach ($viewAsUsers as $viewAsUser)
                                            <option{{ ($selectedViewAsUser == $viewAsUser->id) ? ' selected' : '' }} value="{{ $viewAsUser->id }}">{{ $viewAsUser->name }}</option>
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

                                {{-- Template --}}
                                <div class="form-group">
                                    <label class="control-label m-r-xs" for="template">Template</label>

                                    <select class="form-control chosen-select" name="template" id="template">
                                        <option></option>
                                        @foreach ($templates as $template)
                                            <option{{ ($selectedTemplate == $template->id) ? ' selected' : '' }} value="{{ $template->id }}">{{ $template->title }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Overdue reason --}}
                                <div class="form-group">
                                    <label class="control-label m-r-xs" for="reason">Last overdue reason</label>

                                    <select class="form-control chosen-select" name="reason" id="reason">
                                        <option></option>
                                        <option value="no_reason" {{ ($selectedOverdue === 'no_reason') ? ' selected' : '' }}>No reason</option>
                                        @foreach ($overdues as $overdue)
                                            <option{{ ($selectedOverdue == $overdue->id) ? ' selected' : '' }} value="{{ $overdue->id }}">{{ $overdue->reason }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- From --}}
                                <div class="form-group">
                                    <label class="control-label m-r-xs" for="from">From</label>

                                    <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                                        <span class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                        <input type="text" class="form-control" name="from" id="from" value="{{ $selectedFromDate }}">
                                    </div>
                                </div>

                                {{-- To --}}
                                <div class="form-group">
                                    <label class="control-label m-r-xs" for="to">To</label>

                                    <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                                        <span class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                        <input type="text" class="form-control" name="to" id="to" value="{{ $selectedToDate }}">
                                    </div>
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
                                                @if($selectedCompleted == 'No')
                                                    <th>Due</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($tasks as $task)
                                                <tr>
                                                    <td class="issue-info">
                                                        <a href="{{ action('TaskController@show', $task) }}">{{ $task->title }}</a>
                                                        @if(! $task->client->paid)
                                                            <span class="label label-danger m-l-md">Not paid</span>
                                                        @endif
                                                        @if(! $task->client->active)
                                                            <span class="label label-danger m-l-md">Not active</span>
                                                        @endif
                                                        @if($task->client->complaint_case)
                                                            <span class="label label-warning m-l-md">Active Complaint Case</span>
                                                        @endif
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
                                                        <span class="label label-{{ $task->deadlineClass() }}" style="{{ $task->isOverdue() && $task->overdueReason ? 'background-color:' . $task->overdueReason->overdueReason->hex : '' }}">@date($task->deadline)</span>
                                                    </td>
                                                    @if($selectedCompleted == 'No')
                                                        <td>
                                                            <span class="label label-{{ $task->dueDateCountDown()['class'] }}">{{ $task->dueDateCountDown()['label'] }}</span>
                                                        </td>
                                                    @endif
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
                                <i class="text-muted">no tasks</i>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
