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
                    <form class="form-horizontal" role="form" method="POST" action="{{ action('FoldersStructureController@saveFolder', $parentFolderId) }}">
                        {{ csrf_field() }}

                        <input type="hidden" name="parent_id" value="{{$parentFolderId}}" />

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
                                <a class="btn btn-white" href="{{ action('FoldersStructureController@index') }}">Cancel</a>
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
