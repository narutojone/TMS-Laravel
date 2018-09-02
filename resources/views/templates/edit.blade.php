@extends('layouts.app')

@section('title', 'Edit ' . $template->title)

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <h2>Edit {{ $template->title }}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url('/dashboard') }}">Dashboard</a>
            </li>
            <li>
                <a href="{{ action('TemplateController@index') }}">Templates</a>
            </li>
            <li>
                <a href="{{ action('TemplateController@show', $template) }}">{{ $template->title }}</a>
            </li>
            <li class="active">
                <strong>Edit</strong>
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
                    <h5>Edit template</h5>
                </div>
                <div class="ibox-content">
                    <form id="form" class="form-horizontal" role="form" method="POST" action="{{ action('TemplateController@update', $template) }}">
                        {{ csrf_field() }}

                        {{-- Title --}}
                        <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="title">Title</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="title" id="title" value="{{ $template->title }}" required autofocus>

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
                                    value="{{ $template->category }}"
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
                                <textarea class="wysiwyg" name="description" id="description">{!! old('description', $template->versions->first()->description) !!}</textarea>
                                @if ($errors->has('description'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('description') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        {{-- Create a new revision/version --}}
                        <div class="form-group{{ $errors->has('version') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="version">Create new revision/version</label>

                            <div class="col-sm-10">
                                <input type="hidden" name="version" value="0" />
                                <input type="checkbox" class="js-switch" id="version" value="1" name="version" />
                                <span class="m-l-lg">Creating a new revision will force users to accept changes before completing the task</span>

                                @if ($errors->has('version'))
                                    <span class="help-block m-b-none">
                                    <strong>{{ $errors->first('version') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group m-b-none">
                            <div class="col-sm-4 col-sm-offset-2">
                                <a class="btn btn-white" href="{{ action('TemplateController@show', $template) }}">Cancel</a>
                                <button class="btn btn-primary" type="submit">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Delete(deactivate) template form --}}
            @if ($template->tasks->count() == 0)
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Danger Zone</h5>
                    </div>
                    <div class="ibox-content">
                        <form role="form" method="POST" action="{{ action('TemplateController@deactivate', $template) }}">
                            {{ csrf_field() }}

                            <button class="btn btn-danger btn-outline" type="submit">Deactivate template</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
