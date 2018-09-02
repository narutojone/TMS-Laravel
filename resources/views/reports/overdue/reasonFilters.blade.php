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
                <strong>Overdue Reasons Filter Report</strong>
            </li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="wrapper wrapper-content">

    {{-- Tasks --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{ $count }} overdue reasons found</h5>
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
                            @if ($overduereasons->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover issue-tracker m-b-none">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Title</th>
                                                <th>Count</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($overduereasons as $overduereason)
                                                <tr>
                                                    <td>
                                                        #{{ $overduereason->id }}
                                                    </td>
                                                    <td class="issue-info">
                                                        {{ $overduereason->title }}
                                                    </td>
                                                    <td>
                                                        {{ $overduereason->count }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                    {{-- Pagination links --}}
                                    <div class="text-center">
                                        {{ $overduereasons->links() }}
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
