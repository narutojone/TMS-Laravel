@extends('layouts.app')

@section('title', $template->title)

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>{{ $template->title }}</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                    <a href="{{ action('TemplateController@index') }}">Templates</a>
                </li>
                <li class="active">
                    <strong>{{ $template->title }}</strong>
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
                            <div class="col-lg-12">
                                <div class="m-b-md">
                                    @if (Auth::user()->isAdmin())
                                        <a href="{{ action('TemplateController@edit', $template) }}" class="btn btn-white btn-xs pull-right">Edit template</a>
                                    @endif
                                    <form method="post" action="{{ action('TemplateController@duplicate', $template) }}">
                                        {{ csrf_field() }}

                                        <button type="submit" class="btn btn-xs btn-warning pull-right m-r-sm">Duplicate Template</button>
                                    </form>
                                    <h2>{{ $template->title }}</h2>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-5">
                                <dl class="dl-horizontal m-b-none">
                                    <dt>Category:</dt> <dd>{{ $template->category }}</dd>
                                </dl>
                            </div>
                            @if (Auth::user()->isAdmin())
                                <div class="col-lg-7">
                                    <dl class="dl-horizontal m-b-none">
                                        <dt>ID:</dt> <dd>{{ $template->id }}</dd>
                                    </dl>
                                </div>
                            @endif
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="row">
                            <div class="col-lg-12">
                                {!! $template->versions->first()->description !!}
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="row">
                            <div class="col-lg-12">
                                @if (Auth::user()->isAdmin())
                                    <a href="{{ action('TemplateSubtaskController@create', $template) }}" class="btn btn-white btn-xs pull-right">Add subtask</a>
                                @endif

                                <h3 id="subtasks">Subtasks</h3>

                                @if ($subtasks->count() > 0)
                                    <ul id="sortable">
                                        @foreach ($subtasks as $subtask)
                                            <li class="vote-item" data-id="{{ $subtask->id }}">
                                                <span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                                                {{ $subtask->title }}
                                                @if (Auth::user()->isAdmin())
                                                    <div class="pull-right">
                                                        <a class="btn btn-xs btn-default pull-right" href="{{ action('TemplateSubtaskController@edit', $subtask) }}">Edit</a>
                                                        <a class="btn btn-xs btn-default pull-right m-r-xs" href="{{ action('TemplateSubtaskController@show', $subtask) }}">Show</a>
                                                    </div>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <i class="text-muted">No subtasks...</i>
                                @endif
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="row">
                            <div class="col-lg-12">
                                @if (Auth::user()->isAdmin())
                                    <a href="{{ route('groups.templates.create', $template) }}" class="btn btn-white btn-xs pull-right">Add group</a>
                                @endif

                                <h3 id="users">Groups</h3>

                                <div class="table-responsive m-t">
                                    @php($groups = $template->groups()->paginate(30))
                                    @if (count($groups))
                                        <table class="table table-hover issue-tracker">
                                            <thead>
                                            <th>#ID</th>
                                            <th>Name</th>
                                            <th class="text-right">Actions</th>
                                            </thead>
                                            <tbody>
                                            @foreach ($groups as $group)
                                                <tr>
                                                    <td>{{ $group->id }}</td>
                                                    <td>{{ $group->name }}</td>

                                                    @if (Auth::user()->isAdmin())
                                                        <td>
                                                            <form role="form" method="POST" action="{{ route('groups.templates.destroy', [$template, $group]) }}">
                                                                {{ csrf_field() }}
                                                                {{ method_field('delete') }}

                                                                <button class="btn btn-danger btn-outline btn-xs pull-right" type="submit">Remove group</button>
                                                            </form>
                                                        </td>
                                                    @endif

                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>

                                        <div class="text-center">
                                            {{ $groups->fragment('groups')->links() }}
                                        </div>
                                    @else
                                        <div class="text-muted">No groups...</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="row">
                            <div class="col-lg-12">
                                <a href="{{ route('template.overdue-reason.create', $template) }}" class="btn btn-white btn-xs pull-right">Add overdue reason</a>
                                <h3 id="users">Overdue reasons</h3>
                                <div class="table-responsive m-t">
                                    @if (count($template->overdueReasons))
                                        <table class="table table-hover issue-tracker">
                                            <thead>
                                                <th>Reason</th>
                                                <th>Trigger type</th>
                                                <th>Trigger counter</th>
                                                <th>Action</th>
                                                <th class="text-right">Actions</th>
                                            </thead>
                                            <tbody>
                                            @foreach ($template->overdueReasons as $templateOverdueReason)
                                                <tr>
                                                    <td>{{ $templateOverdueReason->overdueReason->reason }}</td>
                                                    <td>{{ $templateOverdueReason->trigger_type }}</td>
                                                    <td>{{ $templateOverdueReason->trigger_counter }}</td>
                                                    <td>{{ $templateOverdueReason->action }}</td>
                                                    @if (Auth::user()->isAdmin())
                                                        <td class="project-actions">
                                                            <a href="{{ route('template.overdue-reason.show', [$template, $templateOverdueReason]) }}" class="btn btn-xs btn-white"><i class="fa fa-edit"></i> Edit</a>
                                                            <form class="inline" role="form" method="POST" action="{{ route('template.overdue-reason.destroy', [$template, $templateOverdueReason]) }}" class="">
                                                                {{ csrf_field() }}
                                                                {{ method_field('delete') }}
                                                                <button type="submit" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Remove</button>
                                                            </form>
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="text-muted">No overdue reasons set. This template uses default overdue reasons.</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="row">
                            <div class="col-lg-12">
                                @if (auth()->user()->isAdmin())
                                    <a href="{{ route('templates.notifications.create', $template) }}" class="btn btn-white btn-xs pull-right">Add notification</a>
                                @endif

                                <h3 id="users">Notifications</h3>

                                <div class="table-responsive m-t">
                                    @if (count($template->notifications))
                                        <table class="table table-hover issue-tracker">
                                            <thead>
                                                <th>#ID</th>
                                                <th>Type</th>
                                                <th>User Type</th>
                                                <th>Before</th>
                                                <th class="text-right">Actions</th>
                                            </thead>
                                            <tbody>
                                            @foreach ($template->notifications as $notification)
                                                <tr>
                                                    <td>{{ $notification->id }}</td>
                                                    <td>{{ ucfirst($notification->type) }}
                                                        @if($notification->type == \App\Repositories\TemplateNotification\TemplateNotification::TYPE_TEMPLATE)<small>({{ \App\Repositories\EmailTemplate\EmailTemplate::find($notification->details['template'])->name }})</small>@endif
                                                    </td>
                                                    <td>{{ ucfirst($notification->user_type) }}</td>
                                                    <td>{{ $notification->before }}</td>
                                                    @if (Auth::user()->isAdmin())
                                                        <td class="project-actions">
                                                            <a href="{{ route('templates.notifications.show', [$template, $notification]) }}" class="btn btn-xs btn-white"><i class="fa fa-folder"></i>View</a>
                                                            <a href="{{ route('templates.notifications.edit', [$template, $notification]) }}" class="btn btn-xs btn-white">Edit</a>
                                                            <a class="btn btn-xs btn-danger" onclick="event.preventDefault(); document.getElementById('notification-delete-{{ $notification->id }}').submit();">Removenotification</a>
                                                            <form id="notification-delete-{{ $notification->id }}" role="form" method="POST" action="{{ route('templates.notifications.destroy', [$template, $notification]) }}" class="hidden">
                                                                {{ csrf_field() }}
                                                                {{ method_field('delete') }}
                                                            </form>
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="text-muted">No notifications...</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        $(function () {
            var sortableObj = $("#sortable");
            sortableObj.sortable({
                update: function (event, ui) {
                    var sortedIDs = sortableObj.sortable("toArray", {attribute: 'data-id'});
                    $.ajax({
                        method: "POST",
                        url: "{{ route('templates.subtasks.sort', $template->id) }}",
                        data: {
                            _token: "{{csrf_token()}}",
                            items: sortedIDs,
                        },
                        beforeSend: function() {
                            sortableObj.addClass('processing');
                        },
                        error: function() {
                            swal('Error!', 'An error occurred while saving', 'error');
                        },
                        complete: function() {
                            sortableObj.removeClass('processing');
                        }
                    });
                }
            });
            sortableObj.disableSelection();
        });
    </script>
@endsection
