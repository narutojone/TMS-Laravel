@extends('reports.template')

@section('report-content')
    <div class="row">
        {{-- Filters --}}
        <div class="col-md-3">
            <form role="form" method="get" action="">
                {{-- Overdue reason --}}
                <div class="form-group">
                    <label class="control-label" for="overdue-reason">Overdue reason</label>

                    <select class="form-control chosen-select" name="overdue-reason" id="overdue-reason" required>
                        <option></option>
                        @foreach ($filters['overdueReasons'] as $overdueReason)
                            <option {{ request('overdue-reason') == $overdueReason->id ? 'selected' : '' }} value="{{ $overdueReason->id }}">{{ $overdueReason->reason }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Min number of occurrences --}}
                <div class="form-group">
                    <label class="control-label" for="counter">Min. No. of occurrences</label>
                    <input type="number" class="form-control" name="counter" id="counter" value="{{ request('counter', 2) }}" min="2" />
                </div>

                {{-- Filters submit button --}}
                <button class="btn btn-primary" type="submit">Filter</button>
            </form>
        </div>

        {{-- Content --}}
        <div class="col-md-9">
            <table class="table table-hover issue-tracker m-b-none">
                <thead>
                <tr>
                    <th>Title</th>
                    <th>Client</th>
                    <th>User</th>
                    <th>Deadline</th>
                    <th>Counter</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($data['tasks'] as $task)
                        <tr>
                            <td class="issue-info">
                                <a href="{{ action('TaskController@show', $task) }}">{{ $task->title }}</a>
                            </td>
                            <td>
                                <a href="{{ action('ClientController@show', $task->client) }}">{{ $task->client->name }}</a>
                            </td>
                            <td style="width: 35%;">
                                @if ($task->user)
                                    <a href="{{ action('UserController@show', $task->user) }}">{{ $task->user->name }}</a>
                                @else
                                    <i class="text-muted">No user</i>
                                @endif
                            </td>
                            <td>
                                <span class="label label-{{ $task->deadlineClass() }}" style="{{ $task->isOverdue() && $task->overdueReason ? 'background-color:' . $task->overdueReason->overdueReason->hex : '' }}">@date($task->deadline)</span>
                            </td>
                            <td class="project-actions">{{ $task->counter }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection