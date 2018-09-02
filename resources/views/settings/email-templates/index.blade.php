@extends('layouts.app')

@section('title', 'Email Templates')

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Email Templates</h2>
        <ol class="breadcrumb">
            <li><a href="{{ url('/dashboard') }}">Dashboard</a></li>
            <li class="active"><strong>Email Templates</strong></li>
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
                            <a data-toggle="modal" data-target="#create-new-folder-modal" class="btn btn-primary btn-xs">
                                Create new folder
                            </a>
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
                        @if (! $templateFolders->isEmpty())
                            <table class="table table-hover">
                                <tbody>
                                    @foreach($templateFolders as $templateFolder)
                                        <tr>
                                            <td class="project-title">
                                                <a href="{{ route('email_templates.detail', $templateFolder) }}"><i class="fa fa-folder"></i> {{ $templateFolder->name }}</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <i class="help-block">No Folders...</i>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="create-new-folder-modal" class="modal fade">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Create new folder</h4>
            </div>
            <form class="form-horizontal" role="form" method="POST" action="{{ route('email_templates.create-folder') }}">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="form-group m-b-none">
                        <input class="input-s-lg" required type="text" id="folder_name" name="folder_name" placeholder="Input folder name" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-default">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
