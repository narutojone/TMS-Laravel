@extends('layouts.app')

@section('title', 'Reviews')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2>Reviews</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li class="active">
                    <strong>Reviews</strong>
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
                        <h5>All reviews</h5>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="project-list">
                        <table class="table table-hover">
                            <thead>
                                <th>ID</th>
                                <th>For user</th>
                                <th>Reviewer</th>
                                <th>Reviewer task</th>
                                <th>Status</th>
                                <th>Reason</th>
                                <th></th>
                                <th class="text-right">Actions</th>
                            </thead>
                            <tbody>
                            @foreach ($reviews as $review)
                                <tr>
                                    <td>{{$review->id}}</td>
                                    <td>
                                        @if (isset($review->userReviewed) && $review->userReviewed)
                                            {{ $review->userReviewed->name }}
                                        @else
                                           N\A
                                        @endif
                                    </td>
                                    <td>
                                        @if (isset($review->reviewer) && $review->reviewer)
                                            {{ $review->reviewer->name }}
                                        @else
                                            N\A
                                        @endif
                                    </td>
                                    <td>
                                        @if (isset($review->reviewerTask) && $review->reviewerTask)
                                            <a href="{{ action('TaskController@show', $review->reviewerTask) }}" class="btn-link">{{ $review->reviewerTask->title }}</a>
                                        @else
                                            N\A
                                        @endif
                                    </td>
                                    <td>
                                        @if ($review->status == \App\Repositories\Review\Review::STATUS_PENDING)
                                            <label class="label label-info">{{$review->status}}</label>
                                        @elseif ($review->status == \App\Repositories\Review\Review::STATUS_APPROVED)
                                            <label class="label label-success">{{$review->status}}</label>
                                        @else <!-- Status declined -->
                                            <label class="label label-danger">{{$review->status}}</label>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $review->reason }}
                                    </td>
                                    <td>
                                        @if ($review->critical)
                                            <label class="label label-danger">Critical</label>
                                        @endif
                                    </td>
                                    <td class="project-actions">
                                        <a href="{{ action('ReviewsController@show', $review) }}" class="btn btn-white btn-sm"><i class="fa fa-folder"></i>View review details</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        <div class="text-center">
                            {{--{{ $users->links() }}--}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection