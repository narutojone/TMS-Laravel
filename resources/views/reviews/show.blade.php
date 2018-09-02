@extends('layouts.app')

@section('title', 'Review for: '. $review->userReviewed->name)

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>
                {{ 'Review for: '. $review->userReviewed->name }}
            </h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                @if ($review->status == App\Repositories\Review\Review::STATUS_PENDING)
                    <li>
                        <a href="{{ action('ReviewsController@pending') }}">Reviews pending</a>
                    </li>
                @else
                    <li>
                        <a href="{{ action('ReviewsController@completed') }}">Reviews completed</a>
                    </li>
                @endif
                <li class="active">
                    <a href="{{ action('UserController@show', $review->userReviewed) }}"> <strong>{{ $review->userReviewed->name }}</strong></a>
                    @if(! $review->status == App\Repositories\Review\Review::STATUS_PENDING)
                        @if($review->userReviewed->level == 1)
                            <span>(From level 1 to 2)</span>
                        @elseif($review->userReviewed->level == 2)
                            <span>(From level 2 to 3)</span>
                        @endif
                    @endif
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
                    <div class="ibox-title">
                        <h5>{{ 'Review deadline: '.$review->reviewerTask->deadline->format('d M Y')  }}</h5>
                    </div>
                    <div class="ibox-title">
                        @if ($review->status == App\Repositories\Review\Review::STATUS_PENDING)
                        <form action="{{ action('ReviewsController@markReviewed', $review->id) }}" method="POST">
                            {{csrf_field()}}
                            <label><input style="margin-right: 10px;" type="checkbox" value="{{ \App\Repositories\Review\Review::CRITICAL_YES  }}" name="critical" {{ $review->critical ? 'checked' : ''  }}>This review has critical errors.</label>
                            <button style="margin-left: 30px;" type="submit" class="btn btn-primary btn-sm">Mark as reviewed</button>
                            <br/><br/>
                            <label>Reason</label>
                            <textarea style="max-width: 370px;" name="reason" placeholder="Reason" class="form-control"></textarea>
                        </form>
                        @else
                            <label class="alert {{($review->status == \App\Repositories\Review\Review::STATUS_APPROVED ? 'alert-success' : 'alert-danger')}}">This review is marked as {{$review->status}}</label>
                        @endif
                    </div>
                </div>
                <div class="ibox-content">
                    <h3 id="tasks">Tasks</h3>

                    <div class="table-responsive m-t">
                        <table class="table table-hover issue-tracker">
                            <thead>
                                <th>Task</th>
                                <th>Client</th>
                                <th>Template</th>
                                <th>Status</th>
                                <th>Completed At</th>
                                <th class="text-right">Actions</th>
                            </thead>
                            <tbody>
                            @foreach ($review->userCompletedTasks as $userCompletedTask)
                            <tr>
                                <td><a href="{{ action('ReviewsController@reviewTask', [$review, $userCompletedTask->id]) }}">{{ $userCompletedTask->task->title }}</a></td>
                                <td><a href="{{ action('ClientController@show', $userCompletedTask->task->client) }}">{{ $userCompletedTask->task->client->name }}</a></td>
                                <td>{{ $userCompletedTask->template->title }}</td>
                                <td>
                                    @if ($userCompletedTask->status == App\Repositories\UserCompletedTask\UserCompletedTask::STATUS_PENDING)
                                        <label class="label label-info">{{strtoupper($userCompletedTask->status)}}</label>
                                    @elseif ($userCompletedTask->status == App\Repositories\UserCompletedTask\UserCompletedTask::STATUS_APPROVED)
                                        <label class="label label-success">{{strtoupper($userCompletedTask->status)}}</label>
                                    @elseif ($userCompletedTask->status == App\Repositories\UserCompletedTask\UserCompletedTask::STATUS_DECLINED)
                                        <label class="label label-danger">{{strtoupper($userCompletedTask->status)}}</label>
                                    @endif
                                </td>
                                <td>{{ $userCompletedTask->created_at }}</td>
                                <td class="project-actions">
                                    <a taget="_blank" href="{{ action('TaskController@show', $userCompletedTask->task) }}" class="btn btn-white btn-sm"><i class="fa fa-folder"></i> View original task</a>
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
@endsection