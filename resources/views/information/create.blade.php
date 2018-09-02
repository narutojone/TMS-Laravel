@extends('layouts.app')

@section('title', 'Create users information')

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Create users information</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url('/dashboard') }}">Dashboard</a>
            </li>
            <li>
                <a href="{{ route('settings.information.index') }}">Users information</a>
            </li>
            <li class="active">
                <strong>Create</strong>
            </li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="wrapper wrapper-content">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Create users information <small>Add a new users information to the system.</small></h5>
                </div>
                <div class="ibox-content">
                    <form id="form" class="form-horizontal" role="form" method="POST" action="{{ route('settings.information.store') }}">
                        {{ csrf_field() }}

                        {{-- Title --}}
                        <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="title">Title</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="title" id="title" value="{{ old('title') }}" required autofocus>

                                @if ($errors->has('title'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        
                        {{-- Visibility --}}
                        <div class="form-group{{ $errors->has('visibility') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="visibility">Visibility</label>

                            <div class="col-sm-10">
                                <select class="form-control chosen-select" name="visibility[]" id="visibility" multiple>
                                    @foreach ($visibilityOptionsArray as $visibilityOption)
                                        <option value="{{ $visibilityOption }}"{{ (old('visibility') == $visibilityOption) ? ' selected' : '' }}>{{ $visibilityOption }}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('visibility'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('visibility') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        {{-- Description --}}
                        <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="description">Description</label>

                            <div class="col-sm-10">
                                @include('quill.create', [
                                    'form' => 'form#form',
                                    'name' => 'description',
                                    'delta' => old('visibility')
                                ])

                                @if ($errors->has('description'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('description') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group m-b-none">
                            <div class="col-sm-4 col-sm-offset-2">
                                <a class="btn btn-white" href="{{ route('settings.information.index') }}">Cancel</a>
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
