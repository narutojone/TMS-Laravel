@extends('layouts.app')

@section('title', 'Users information')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2></h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li class="active">
                    <strong>User information</strong>
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
                        <h5>User information</h5>
                    </div>
                    <div class="ibox-content">
                        <form role="form" class="form-inline" method="get" action="">
                            <label class="control-label m-l-md m-r-xs" for="search">Search</label>
                            <input class="form-control" type="text" name="search" id="search" value="{{ request('search', '') }}">
                            <button class="btn btn-primary m-l-md" type="submit">Filter</button>
                        </form>
                        <div class="hr-line-dashed"></div>

                        @if ($information->count() > 0)
                            <div class="project-list">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Read at</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($information as $info)
                                        <tr>
                                            <td class="project-title">
                                                <a href="{{ route('information.show', $info) }}">{{ $info->title }}</a>
                                            </td>
                                            <td>
                                                {{ $info->pivot->created_at->format('Y-m-d') }}
                                            </td>
                                            <td>
                                                @if($info->pivot->accepted_status == 0)
                                                    <button type="button" class="btn btn-danger btn-xs">Declined</button>
                                                @endif
                                                @if($info->pivot->accepted_status == 1)
                                                    <button type="button" class="btn btn-success btn-xs">Accepted</button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                <div class="text-center">
                                    {{ $information->links() }}
                                </div>
                            </div>
                        @else
                            <i class="text-muted">no users information</i>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
