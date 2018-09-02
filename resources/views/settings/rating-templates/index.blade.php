@extends('layouts.app')

@section('title', 'Rating Templates')

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Rating Templates</h2>
        <ol class="breadcrumb">
            <li><a href="{{ url('/dashboard') }}">Dashboard</a></li>
            <li class="active"><strong>Rating Templates</strong></li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="wrapper wrapper-content animated fadeInUp">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Rating Templates</h5>
                    <div class="ibox-tools">
                        @superAdmin
                            <a href="{{ route('rating_templates.create') }}" class="btn btn-primary btn-xs">Add new rating template</a>
                        @endif
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="project-list">
                        @if (! $templates->isEmpty())
                            <table class="table table-hover">
                                <thead>
                                    <th>Subject</th>
                                    <th>Email template</th>
                                    <th>Tasks completed</th>
                                    <th>Days from last review</th>
                                    <th class="text-right">Actions</th>
                                </thead>
                                <tbody>
                                    @foreach($templates as $template)
                                        <tr>
                                            <td class="project-title">
                                                {{ ucfirst($template->subject) }}
                                            </td>
                                            <td class="project-title">
                                                {{ optional($template->emailTemplate)->title }}
                                            </td>
                                            <td class="project-title">
                                                {{ $template->tasks_completed }}
                                            </td>
                                            <td class="project-title">
                                                {{ $template->days_from_last_review }}
                                            </td>
                                            <td class="project-actions">
                                                <a href="{{ route('rating_templates.show', $template) }}" class="btn btn-white btn-sm"><i class="fa fa-folder"></i> Preview</a>
                                                @superAdmin
                                                    <a href="{{ route('rating_templates.edit', $template) }}" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i> Edit</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="text-center">
                                {{ $templates->links() }}
                            </div>
                        @else
                            <i class="help-block text-muted'">No items...</i>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
