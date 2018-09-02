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
                        <h5>All users information</h5>

                        @if (Auth::user()->isAdmin())
                            <div class="ibox-tools">
                                <a href="{{ route('settings.information.create') }}" class="btn btn-primary btn-xs">Create new information</a>
                            </div>
                        @endif
                    </div>
                    <div class="ibox-content">
                        @if ($information->count() > 0)
                            <div class="project-list">
                                <table class="table table-hover">
                                    <tbody>
                                    @foreach ($information as $info)
                                        <tr>
                                            <td class="project-title">
                                                <a href="{{ route('settings.information.show', $info) }}">{{ $info->title }}</a>
                                            </td>
                                            <td class="project-actions">
                                                <a href="{{ route('settings.information.show', $info) }}" class="btn btn-white btn-sm">
                                                    <i class="fa fa-folder"></i> View
                                                </a>
                                                @if (Auth::user()->isAdmin())
                                                    <a href="{{ route('settings.information.edit', $info) }}" class="btn btn-white btn-sm">
                                                        <i class="fa fa-pencil"></i> Edit
                                                    </a>
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
