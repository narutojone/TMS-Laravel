@extends('layouts.app')

@section('title', 'Systems')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2></h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li class="active">
                    <strong>Systems</strong>
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
                        <h5>System list</h5>

                        <div class="ibox-tools">
                            <a href="{{ route('systems.create') }}" class="btn btn-primary btn-xs">Create new system</a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="project-list">
                            <table class="table table-hover">
                                <thead>
                                    <th>#ID</th>
                                    <th>Name</th>
                                    <th>Visible</th>
                                    <th>Default</th>
                                    <th>Actions</th>
                                </thead>
                                <tbody>
                                    @forelse($systems as $system)
                                        <tr>
                                            <td>{{ $system->id }}</td>
                                            <td class="project-title">
                                                <a href="{{ route('systems.show', $system) }}">
                                                    {{ $system->name }}
                                                </a>
                                            </td>
                                            <td class="project-title">
                                                @if ($system->visible)
                                                    <span class="label label-success">Visible</span>
                                                @else
                                                    <span class="label label-danger">Not Visible</span>
                                                @endif
                                            </td>
                                            <td class="project-title">
                                                @if ($system->default)
                                                    <span class="label label-success">Default</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="project-actions">
                                                <a href="{{ route('systems.show', $system) }}" class="btn btn-white btn-sm">
                                                    <i class="fa fa-folder"></i> View
                                                </a>
                                                <a href="{{ route('systems.edit', $system) }}" class="btn btn-white btn-sm">
                                                    <i class="fa fa-pencil"></i> Edit
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td class="text-muted">No systmes...</td></tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <div class="text-center">
                                {{ $systems->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
