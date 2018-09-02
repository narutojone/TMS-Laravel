@extends('layouts.app')

@section('title', 'Rating Report')

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Rating Reports</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url('/dashboard') }}">Dashboard</a>
            </li>
            <li>
                Reports
            </li>
            <li class="active">
                <strong>Rating Report</strong> (Score: {{ $average }})
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
                    <form role="form" class="form-inline" method="get" action="">
                        {{-- Filter --}}
                        <label class="control-label" for="rate">Filter by rating: </label>

                        <select name="rate" class="form-control" >
                            <option value="">All</option>
                            @foreach($orderOptions as $ordKey=>$ordValue)
                                <option value="{{$ordKey}}" {{($ordKey == app('request')->input('rate',6 )) ? 'selected="selected"' : ''}}>{{$ordValue}}</option>
                            @endforeach
                        </select>

                        <label class="control-label m-l-md" for="rate">Filter by rater: </label>
                        <select name="type" class="form-control">
                            <option value="">Both</option>
                            <option value="client" {{(app('request')->input('type')) == 'client' ? 'selected="selected"' : ''}}>Client</option>
                            <option value="employee" {{(app('request')->input('type')) == 'employee' ? 'selected="selected"' : ''}}>Employee</option>
                        </select>
                            
                        <button class="btn btn-primary m-l-md" type="submit">Filter</button>
                    </form>
                    <div class="hr-line-dashed"></div>
                    @if ($ratings->count() > 0)
                        <div class="project-list">
                            <table class="table table-hover">
                                <thead>
                                    <th>
                                        #
                                    </th>
                                    <th>
                                        Info
                                    </th>
                                    <th>
                                        Rating
                                    </th>
                                    <th>
                                        Feedback
                                    </th>
                                </thead>
                                <tbody>
                                    @foreach ($ratings->get() as $rating)
                                        <tr>
                                            <td>
                                                @if (! $rating->reviewed)
                                                    <a href="{{ route('ratings.review', $rating) }}" class="btn btn-info btn-xs">Review</a>
                                                @else
                                                    <span class="label label-success">Reviewed</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $rating->commentable->name }}
                                                <br>
                                                <small><strong> {{ strpos($rating->ratingable_type, 'Client') ? 'Client: ' : 'Employee: ' }} </strong> {{ $rating->ratingable->name }} (PF{{ $rating->ratingable->pf_id }}) - <strong>Rated at:</strong> {{$rating->created_at}}</small>
                                            </td>
                                            <td>
                                                @if($rating->rate == 1)
                                                    <span class="label" style="background-color: #e22027; color: #ffffff;">1 Star</span>
                                                @elseif($rating->rate == 2)
                                                    <span class="label" style="background-color: #f47324; color: #ffffff;"">{{ $rating->rate }} Stars</span>
                                                @elseif($rating->rate == 3)
                                                    <span class="label" style="background-color: #f8cc18; color: #ffffff;"">{{ $rating->rate }} Stars</span>
                                                @elseif($rating->rate == 4)
                                                    <span class="label" style="background-color: #73b143; color: #ffffff;"">{{ $rating->rate }} Stars</span>
                                                @elseif($rating->rate == 5)
                                                    <span class="label" style="background-color: #007f4e; color: #ffffff;"">{{ $rating->rate }} Stars</span>
                                                @endif
                                            </td>
                                            <td class="issue-info">
                                                {{ $rating->feedback }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <i class="text-muted">no ratings</i>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
