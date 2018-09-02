@extends('layouts.app')

@section('title', $user->name)

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>
                {{ $user->name }}
            </h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                    <a href="{{ action('UserController@index') }}">Users</a>
                </li>
                <li class="active">
                    <strong>{{ $user->name }}</strong>
                </li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    @if($user->out_of_office)
        <div class="row">
            <div class="col-lg-12">
                <div class="wrapper wrapper-content animated fadeInUp" style="padding-bottom: 0;">
                    <div class="alert alert-warning m-b-none">
                        Please note that {{ $user->name }} is currently out of office!
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-lg-12">
            <div class="wrapper wrapper-content">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="m-b-md">
                                    @if(Auth::user()->isAdmin())
                                        @if($user->active)
                                            <a href="{{ route('users.ooo.create', $user) }}" class="btn btn-danger btn-xs pull-right m-l-sm">Set out of office</a>
                                            <a href="{{ route('flag.user.create', $user) }}" class="btn btn-danger btn-xs pull-right m-l-sm">Flag user</a>
                                        @endif
                                        <span class="pull-right">&nbsp;&nbsp;</span>
                                        <a href="{{ action('UserWorkloadController@edit', $user) }}" class="btn btn-white btn-xs pull-right m-l-sm">Update workload</a>
                                        <a href="{{ action('UserController@edit', $user) }}" class="btn btn-white btn-xs pull-right m-l-sm">Edit user</a>
                                    @endif
                                    <h2>
                                        {{ $user->name }}
                                        @if ($user->hasFlags())
                                            @include('flag-user.flagged', ['color' => $user->flagColor()])
                                        @endif
                                    </h2>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-5">
                                <dl class="dl-horizontal m-b-none">
                                    <dt>Level:</dt> <dd>{{ $user->level }}</dd>
                                </dl>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-5">
                                <dl class="dl-horizontal m-b-none">
                                    <dt>Email:</dt> <dd><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></dd>
                                </dl>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-5">
                                <dl class="dl-horizontal m-b-none">
                                    <dt>PF ID:</dt> <dd>{{ !is_null($user->pf_id) ? $user->pf_id : '- none set -' }}</dd>
                                </dl>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-5">
                                <dl class="dl-horizontal m-b-none">
                                    <dt>Phone:</dt> <dd>{{ $user->phone ?: '- none set -' }}</dd>
                                </dl>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-5">
                                <dl class="dl-horizontal m-b-none">
                                    @php ( $customersCount = $user->clients(false)->count() )
                                    <dt>Existing customers:</dt> <dd>{{ $customersCount > 0 ? $customersCount : 0  }}</dd>
                                </dl>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-5">
                                <dl class="dl-horizontal m-b-none">
                                    <dt>Customer capacity:</dt> <dd>{{$user->customer_capacity}}</dd>
                                </dl>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-5">
                                <dl class="dl-horizontal m-b-none">
                                    <dt>Weekly Hour Capacity:</dt> <dd>{{$user->weekly_capacity}}</dd>
                                </dl>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-5">
                                <dl class="dl-horizontal m-b-none">
                                    <dt>Customer types:</dt>
                                    <dd>
                                        @if($user->customerTypes->count() > 0)
                                            @foreach($user->customerTypes as $customerType)
                                                {{$customerType->name}}<br/>
                                            @endforeach
                                        @else
                                            - none set -
                                        @endif
                                    </dd>
                                </dl>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-5">
                                <dl class="dl-horizontal m-b-none">
                                    <dt>Systems:</dt>
                                    <dd>
                                        @if($user->systems->count() > 0)
                                            @foreach($user->systems as $system)
                                                {{$system->name}}<br/>
                                            @endforeach
                                        @else
                                            - none set -
                                        @endif
                                    </dd>
                                </dl>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-5">
                                <dl class="dl-horizontal m-b-none">
                                    <dt>Type of tasks:</dt>
                                    <dd>
                                        @if($user->taskTypes->count() > 0)
                                            @foreach($user->taskTypes as $taskType)
                                                {{$taskType->name}}<br/>
                                            @endforeach
                                        @else
                                            - none set -
                                        @endif
                                    </dd>
                                </dl>
                            </div>
                        </div>

                        @if($user->client_id || $thisWeekTasks->count() || $nextWeekTasks->count() || $threeWeeksTasks->count() || $fourWeeksTasks->count())
                            <div class="hr-line-dashed"></div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="pull-right">
                                        <a href="{{ action('ClientController@completed', $user->client_id) }}" class="btn btn-white btn-xs">Completed tasks</a>
                                        @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                                            <div class="btn-group align-right">
                                                <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle">Add task <span class="caret"></span></button>
                                                <ul class="dropdown-menu">
                                                    <li><a href="{{ action('TaskController@create', $user->client_id) }}">From template</a></li>
                                                    <li><a href="{{ action('TaskController@createCustom', $user->client_id) }}">Custom</a></li>
                                                </ul>
                                            </div>
                                        @else
                                            <div class="btn-group align-right">
                                                <a href="{{ action('TaskController@createCustom', $user->client_id) }}" class="btn btn-primary btn-xs">Add task</a>
                                            </div>
                                        @endif
                                    </div>
                                    <h3 id="tasks">Tasks</h3>

                                    <div class="row">
                                        <div class="col-lg-12">

                                            <div class="row">
                                                <div class="col-lg-5">
                                                    <dl class="dl-horizontal m-b-none">
                                                        <dt>This week:</dt> <dd>@if($thisWeekTasks->count()) <a href="{{ action('UserController@weekTasks', ['user'=> $user->id, 'week' => 1]) }}">{{ $thisWeekTasks->count() }} tasks</a> @else {{ $thisWeekTasks->count() }} tasks @endif</dd>
                                                    </dl>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-5">
                                                    <dl class="dl-horizontal m-b-none">
                                                        <dt>Next week:</dt> <dd>@if($nextWeekTasks->count()) <a href="{{ action('UserController@weekTasks', ['user'=> $user->id, 'week' => 2]) }}">{{ $nextWeekTasks->count() }} tasks</a> @else {{ $nextWeekTasks->count() }} tasks @endif</dd></dd>
                                                    </dl>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-5">
                                                    <dl class="dl-horizontal m-b-none">
                                                        <dt>Three weeks:</dt> <dd>@if($threeWeeksTasks->count()) <a href="{{ action('UserController@weekTasks', ['user'=> $user->id, 'week' => 3]) }}">{{ $threeWeeksTasks->count() }} tasks</a> @else {{ $threeWeeksTasks->count() }} tasks @endif</dd></dd>
                                                    </dl>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-5">
                                                    <dl class="dl-horizontal m-b-none">
                                                        <dt>Four weeks:</dt> <dd>@if($fourWeeksTasks->count()) <a href="{{ action('UserController@weekTasks', ['user'=> $user->id, 'week' => 4]) }}">{{ $fourWeeksTasks->count() }} tasks</a> @else {{ $fourWeeksTasks->count() }} tasks @endif</dd></dd>
                                                    </dl>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive m-t">
                                        <table class="table table-hover issue-tracker">
                                            <tbody>
                                                @forelse ($tasks as $task)
                                                    @if ( ! Auth::user()->admin && ($task->client->manager_id != Auth::user()->id) && $task->taskOverdueReasons()->orderBy('created_at', 'DESC')->first() && ! $task->taskOverdueReasons()->orderBy('created_at', 'DESC')->first()->overdueReason->visible)
                                                        @continue
                                                    @endif
                                                    <tr>
                                                        <td class="issue-info">
                                                            <a href="{{ action('TaskController@show', $task) }}">{{ $task->title }}</a>
                                                            <small>{{ $task->category }}</small>
                                                        </td>
                                                        <td>
                                                            @if ($task->user)
                                                                @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                                                                    <a href="{{ action('UserController@show', $task->user) }}">{{ $task->user->name }}</a>
                                                                @else
                                                                    {{ $task->user->name }}
                                                                @endif
                                                            @else
                                                                <i class="text-muted">no user</i>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="label label-{{ $task->deadlineClass() }}" style="{{ $task->isOverdue() && $task->overdueReason ? 'background-color:' . $task->overdueReason->overdueReason->hex : '' }}">@date($task->deadline)</span>
                                                        </td>
                                                        <td>
                                                            <span class="label label-{{ $task->dueDateCountDown()['class'] }}">{{ $task->dueDateCountDown()['label'] }}</span>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <i class="text-muted">no internal tasks</i>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="hr-line-dashed"></div>

                        <div class="row">
                            <div class="col-lg-12">
                                <h3>User Groups</h3>
                                @forelse ($user->groups as $group)
                                    <table class="table table-hover">
                                        <tbody>
                                        <tr>
                                            <td class="project-title">
                                                @if (auth()->user()->isAdmin())
                                                    <a href="{{ route('groups.show', $group) }}">{{ $group->name }}</a>
                                                @else
                                                    {{ $group->name }}
                                                @endif
                                            </td>
                                            <td class="project-actions">
                                                @if (auth()->user()->isAdmin())
                                                    <a href="{{ route('groups.show', $group) }}" class="btn btn-white btn-sm">
                                                        <i class="fa fa-folder"></i> View
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                @empty
                                    <i class="text-muted">no groups</i>
                                @endforelse
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="row">
                            <div class="col-lg-12">
                                @if ($clients->count() > 0)
                                    <h3 id="client">Assigned Clients</h3>
                                    <table class="table table-hover">
                                        <tbody>
                                        @foreach ($clients as $client)
                                            <tr>
                                                <td class="project-title">
                                                    <a href="{{ action('ClientController@show', $client->id) }}">{{ $client->name }}</a>  <span style='margin-left:20px;' class="label label-danger">{{$client->risk ? 'High risk' : ''}}</span>
                                                    @if( ! $client->active)
                                                        <span style='margin-left:20px' class="label label-danger">Deactivated</span>
                                                    @endif
                                                    @if ($client->manager_id == Auth::user()->id)
                                                        <span class="label label-info">Manager</span>
                                                    @endif
                                                    <br>
                                                    @if ($client->system)
                                                        <small>{{ $client->system->name }}</small>
                                                    @endif        
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
                                    <div class="text-center">
                                        {{ $clients->fragment('clients')->links() }}
                                    </div>
                                @else
                                    <i class="text-muted">no clients</i>
                                @endif
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="row">
                            <div class="col-lg-12">
                                <h3>All flags</h3>
                                <table class="table table-hover">
                                    @if ($user->hasFlags())
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Flag title</th>
                                            <th>Flag date</th>
                                            <th>Valid To</th>
                                            <th>Client</th>
                                            <th>Comment</th>
                                            <th>Status</th>
                                            <th class="project-actions">Actions</th>
                                        </tr>
                                    </thead>
                                    @endif
                                    <tbody>
                                    @forelse ($user->flags()->withTrashed()->orderBy('pivot_created_at', 'desc')->get() as $flag)
                                        <tr>
                                            <td>@include('flag-user.flagged', ['color' => $flag->hex])</td>
                                            <td class="project-title">
                                                {{ $flag->reason }}
                                            </td>
                                            <td>
                                                {{ Carbon\Carbon::parse($flag->pivot->created_at)->format('Y-m-d') }}
                                            </td>
                                            <td>
                                                {{ $flag->validTo() }}
                                            </td>
                                            <td>
                                                @if($flag->pivot->client_id)
                                                    {{ App\Repositories\Client\Client::find($flag->pivot->client_id)->name }}
                                                @else
                                                   N/A
                                                @endif
                                            </td>
                                            <td>
                                                {{ $flag->pivot->comment }}
                                            </td>
                                            <td>
                                                @if ($flag->pivot->active == 1)
                                                    <span class="label label-success">Active</span>
                                                @else
                                                    <span class="label label-default">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="project-actions">
                                                @if (Auth::user()->isAdmin() && $flag->isActive())
                                                    <form method="POST" action="{{ route('flag.user.destroy', [$flag, $user]) }}">
                                                        {{ csrf_field() }}
                                                        {{ method_field('delete') }}
                                                        <button class="btn btn-white btn-sm">
                                                            <i class="fa fa-pencil"></i> Reset
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <span><em>no flags...</em></span>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>
                        <div class="row">
                            <div class="col-md-12">
                                <h3 class="m-t-none m-b">Upcoming and Current (Out Of Office)</h3>
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>From date</th>
                                        <th>To date</th>
                                        <th>Reason</th>
                                        <th>Created at</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($outOfOffice as $ooo)
                                        @if(\Carbon\Carbon::parse($ooo->to_date)->gte(\Carbon\Carbon::now()))
                                            <tr>
                                                <td>{{ $ooo->id }}</td>
                                                <td>{{ $ooo->from_date }}</td>
                                                <td>{{ $ooo->to_date }}</td>
                                                <td>{{ $ooo->reason->name }}</td>
                                                <td class="text-navy">{{ $ooo->created_at }}</td>
                                                <td>
                                                    <form class="form-horizontal" role="form" method="POST" action="{{ action('UserController@removeOoo', $user) }}">
                                                        {{ csrf_field() }}
                                                        <input type="hidden" name="ooo" value="{{ $ooo->id }}" />
                                                        <input type="submit" value="Remove" class="btn btn-danger btn-xs btn-outline" />
                                                    </form>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>

                                <h3 class="m-t-none m-b">Past (Out Of Office)</h3>
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>From date</th>
                                        <th>To date</th>
                                        <th>Reason</th>
                                        <th>Created at</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($outOfOffice as $ooo)
                                        @if(\Carbon\Carbon::parse($ooo->to_date)->lt(\Carbon\Carbon::now()))
                                            <tr>
                                                <td>{{ $ooo->id }}</td>
                                                <td>{{ $ooo->from_date }}</td>
                                                <td>{{ $ooo->to_date }}</td>
                                                <td>{{ $ooo->reason->name }}</td>
                                                <td class="text-navy">{{ $ooo->created_at }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection