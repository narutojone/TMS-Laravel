@extends('layouts.app')

@section('title', 'Groups')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2></h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li class="active">
                    <strong>Gorups</strong>
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
                        <h5>Groups list</h5>

                        <div class="ibox-tools">
                            <a href="{{ route('groups.create') }}" class="btn btn-primary btn-xs">Create new group</a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="project-list">
                            <table class="table table-hover">
                                <thead>
                                    <th>#ID</th>
                                    <th>Name</th>
                                    <th>Actions</th>
                                </thead>
                                <tbody>
                                    @forelse($groups as $group)
                                        <tr>
                                            <td>{{ $group->id }}</td>
                                            <td class="project-title">
                                                <a href="{{ route('groups.show', $group) }}">
                                                    {{ $group->name }}
                                                </a>
                                            </td>
                                            <td class="project-actions">
                                                <a href="{{ route('groups.show', $group) }}" class="btn btn-white btn-sm">
                                                    <i class="fa fa-folder"></i> View
                                                </a>
                                                <a href="{{ route('groups.edit', $group) }}" class="btn btn-white btn-sm">
                                                    <i class="fa fa-pencil"></i> Edit
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td>No template groups...</td></tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <div class="text-center">
                                {{ $groups->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
