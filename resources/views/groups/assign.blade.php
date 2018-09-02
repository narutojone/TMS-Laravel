@extends('layouts.app')

@section('title', 'Assign group to template')

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Assign group to template</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url('/dashboard') }}">Dashboard</a>
            </li>
            <li>
                <a href="{{ action('TemplateController@index') }}">Templates</a>
            </li>
            <li>
                <a href="{{ action('TemplateController@show', $template) }}">Template {{ $template->title }}</a>
            </li>
            <li class="active">
                <strong>Assign</strong>
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
                    <h5>Assign new group to template</h5>
                </div>
                <div class="ibox-content">
                    <form id="form" class="form-horizontal" role="form" method="POST" action="{{ route('groups.templates', $template) }}">
                        {{ csrf_field() }}

                        {{-- Groups --}}
                        <div class="form-group{{ $errors->has('groups') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="groups">Groups</label>
                            <div class="col-sm-10">
                                <select class="form-control chosen-select" name="groups[]" id="groups" multiple>
                                    @foreach ($groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('groups'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('groups') }}</strong>
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
        </div>
    </div>
</div>
@endsection
