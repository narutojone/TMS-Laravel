@extends('layouts.app')

@section('title', 'Add new folder')

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Add new folder</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url('/dashboard') }}">Dashboard</a>
            </li>
            <li class="active">
                <strong>Folders structure</strong>
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
                    <h5>Add new folder</h5>
                </div>
                <div class="ibox-content">
                    <form class="form-horizontal" role="form" method="POST" action="{{ action('LibraryController@saveFolder', $parentFolderId) }}">
                        {{ csrf_field() }}

                        <input type="hidden" name="parent_id" value="{{$parentFolderId}}" />

                        {{-- Visibility - if set to '0' then only admins can view it --}}
                        <div class="form-group{{ $errors->has('visible') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="can_get_tasks">Visible</label>

                            <div class="col-sm-10">
                                <input type="checkbox" class="js-switch" id="visible" name="visible" checked>

                                @if ($errors->has('visible'))
                                    <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('visible') }}</strong>
                                        </span>
                                @endif
                            </div>
                        </div>

                        {{-- Parent folder name --}}
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Parent folder:</label>
                            <div class="col-sm-10" style="padding-top:7px;">{{$parentFolder}}</div>
                        </div>


                        {{-- Folder name --}}
                        <div class="form-group{{ $errors->has('note') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="name">Folder name</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="name" id="name"></textarea>

                                @if ($errors->has('name'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>



                        <div class="form-group m-b-none">
                            <div class="col-sm-4 col-sm-offset-2">
                                <a class="btn btn-white" href="{{ action('LibraryController@indexSettings') }}">Cancel</a>
                                <button class="btn btn-primary" type="submit">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
