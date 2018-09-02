@extends('layouts.app')

@section('title', 'Create template')

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Create template</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url('/dashboard') }}">Dashboard</a>
            </li>
            <li>
                <a href="{{ action('TemplateController@index') }}">Templates</a>
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
                    <h5>Create template <small>Add a new template to the system.</small></h5>
                </div>
                <div class="ibox-content">
                    <form id="form" class="form-horizontal" role="form" method="POST" action="{{ action('TemplateController@store') }}">
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
                        
                        {{-- Category --}}
                        <div class="form-group{{ $errors->has('category') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="category">Category</label>

                            <div class="col-sm-10">
                                <input type="text"
                                    class="form-control"
                                    name="category"
                                    id="category"
                                    data-provide="typeahead"
                                    data-source='{{ $categories }}'
                                    value="{{ old('category') }}"
                                    autocomplete="off"
                                    required>

                                @if ($errors->has('category'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('category') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        {{-- Description --}}
                        <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="description">Description</label>

                            <div class="col-sm-10">
                                <textarea class="wysiwyg" name="description" id="description">{!! old('description') !!}</textarea>
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
                                <a class="btn btn-white" href="{{ action('TemplateController@index') }}">Cancel</a>
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
