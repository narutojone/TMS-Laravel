@extends('layouts.app')

@section('title', 'Review Task')

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-10">
        <h2>Reviews</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url('/dashboard') }}">Dashboard</a>
            </li>
            <li>
                <a href="{{ action('ReviewsController@pending') }}">Reviews</a>
            </li>
            <li>
                <a href="{{ url('/reviews/'.$review->id) }}">Review for {{ $review->userReviewed->name }}</a>
            </li>
            <li>
                <a href="{{ url('/reviews/'.$review->id.'/tasks/'.$userCompletedTask->id) }}">{{ $userCompletedTask->task->title }}</a>
            </li>
            <li class="active">
                Subtask - {{ $userCompletedSubtask->subtask->title }}
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
                            <div class="col-lg-5">
                                <dl class="dl-horizontal m-b-none">
                                    <dt>User:</dt> <dd>
                                        @if ($userCompletedSubtask->subtask->user)
                                            {{ $userCompletedSubtask->subtask->user->name }}
                                        @elseif ($userCompletedTask->task->user)
                                            {{ $userCompletedTask->task->user->name }}
                                        @else
                                            <i class="text-muted">no user</i>
                                        @endif
                                    </dd>
                                    <dt>Deadline:</dt> <dd>
                                        @if ($userCompletedSubtask->subtask->deadline)
                                            @date($userCompletedSubtask->subtask->deadline)
                                        @elseif ($userCompletedTask->task->deadline)
                                            @date($userCompletedTask->task->deadline)
                                        @endif
                                    </dd>
                                </dl>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        @if ($userCompletedSubtask->status == \App\Repositories\UserCompletedSubtask\UserCompletedSubtask::STATUS_PENDING)
                            <div class="row">
                                <div class="col-lg-12">
                                    <form method="POST" class="m-l-xl">
                                        {{ csrf_field() }}
                                        <div class="col-lg-6">
                                            <button type="submit" formaction="{{ action('ReviewsController@approveSubtask', [$review, $userCompletedSubtask->id]) }}" class="btn btn-xs btn-warning">Approve Subtask</button>
                                        </div>
                                        <div class="col-lg-6">
                                            <button style="margin-left: 30px;" type="submit" formaction="{{ action('ReviewsController@declineSubtask', [$review, $userCompletedSubtask->id]) }}" class="btn btn-xs btn-warning">Decline Subtask</button>
                                            <br/><br/>
                                            <label>Reason</label>
                                            <textarea name="reason" placeholder="Reason" class="form-control"></textarea>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                        @else
                            <div class="row">
                                <div class="col-lg-12">
                                    <label class="alert {{ $userCompletedSubtask->status == \App\Repositories\UserCompletedSubtask\UserCompletedSubtask::STATUS_APPROVED ? 'alert-success' : 'alert-danger'  }}">{{ strtoupper($userCompletedSubtask->status) }}</label>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                        @endif

                        {{-- Details --}}
                        @if ($userCompletedSubtask->subtask->isComplete())
                            <div class="row">
                                <div class="col-lg-5">
                                    <dl class="dl-horizontal m-b-none">
                                        <dt>Completed at:</dt> <dd>@date($userCompletedSubtask->subtask->completed_at)</dd>
                                    </dl>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                        @endif

                        {{-- Description --}}
                        <div class="row">
                            <div class="col-lg-12">
                                {!! $userCompletedSubtask->subtask->version->description !!}
                            </div>
                        </div>

                        {{-- Files --}}
                        @if ($userCompletedSubtask->subtask->files)
                            <div class="row">
                                <div class="col-lg-5">
                                    @foreach ($userCompletedSubtask->subtask->files as $file)
                                        <dl class="dl-horizontal m-b-none">
                                            <dt>{{$file->created_at}}:</dt>
                                            <dd><a href="{{FileVault::publicUrl($file->filevault_id)}}" target="_blank">File: {{$file->name}}</a></dd>
                                        </dl>
                                    @endforeach
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                        @endif
                    </div>
                </div>

                {{-- Reopenings --}}
                @if ($userCompletedSubtask->subtask->reopenings()->count() > 0)
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Reopenings</h5>
                        </div>
                        <div class="ibox-content">
                            <ul class="list-unstyled">
                                @foreach ($userCompletedSubtask->subtask->reopenings()->orderBy('created_at', 'desc')->get() as $reopening)
                                    <li{{ (!$loop->last) ? ' class=m-b' : '' }}>
                                        <strong>
                                            @if ($reopening->reason)
                                                {{ $reopening->reason }}
                                            @else
                                                <i>no reason specified</i>
                                            @endif
                                        </strong>
                                        <br>
                                        Old completion date: @datetime($reopening->completed_at)
                                        <br>
                                        <small class="text-muted">@datetime($reopening->created_at)</small>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection