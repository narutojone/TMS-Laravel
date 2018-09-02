@extends('layouts.app')

@section('title', 'Email Templates')

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Email Templates</h2>
        <ol class="breadcrumb">
            <li><a href="{{ url('/dashboard') }}">Dashboard</a></li>
            <li><a href="{{ url('/system_settings/email_templates') }}">Emaail Templates</a></li>
            <li class="active"><strong>{{ $folder_name }}</strong></li>
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
                    <h5>Email Templates</h5>
                    <div class="ibox-tools">
                        @superAdmin
                            <a href="{{ route('email_templates.add', $folderId) }}" class="btn btn-primary btn-xs">Add template</a>
                            <a href="{{ route('email_templates.showall') }}" class="btn btn-primary btn-xs">Show all templates</a>
                        @endif
                    </div>
                </div>
                <div class="ibox-content">
                    <form role="form" class="form-inline" method="get" action="{{ route('email_templates.search') }}">
                        {{-- Filter --}}
                        <input type="hidden" id="folder_id" name="folder_id" value="{{ $folderId }}" />
                        <input class="m-r-sm" type="text" id="template_name" name="template_name" placeholder="Template name"/>
                        <input type="checkbox" id="show_deactivated" name="show_deactivated" value="1" {{ app('request')->input('show_deactivated', 0 ) == 1 ? 'checked' : '' }} />
                        <label class="control-label m-r-xs" for="show_deactivated">Show deactivated</label>
                        <button class="btn btn-xs btn-primary m-l-md" type="submit">Filter</button>
                    </form>
                    <div class="hr-line-dashed"></div>


                    <div class="project-list">
                        @if (! $templates->isEmpty())
                            <table class="table table-hover">
                                <thead>
                                    <th>Name</th>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th class="text-right">Actions</th>
                                </thead>
                                <tbody>
                                    @foreach($templates as $template)
                                        <tr>
                                            <td class="project-title">{{ $template->name }}</td>
                                            <td class="project-title">{{ $template->title }}</td>
                                            <td class="project-title">
                                                @if($template->active)
                                                    <span class="label label-primary">Active</span>
                                                @else
                                                    <span class="label label-danger">Deactivated</span>
                                                @endif
                                            </td>
                                            <td class="project-actions">
                                                <a href="{{ route('email_templates.show', $template) }}" class="btn btn-white btn-sm"><i class="fa fa-folder"></i> Preview</a>
                                                <a href="{{ route('email_templates.send', $template) }}" class="btn btn-white btn-sm"><i class="fa fa-send"></i> Send test email</button>
                                                @superAdmin
                                                    <a href="{{ route('email_templates.edit', $template) }}" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i> Edit</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="text-center">
                                {{ $templates->appends(request()->except('page'))->links() }}
                            </div>
                        @else
                            <i class="help-block">No templates...</i>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
