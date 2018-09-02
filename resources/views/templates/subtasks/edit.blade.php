@extends('layouts.app')

@section('title', 'Edit ' . $subtask->title)

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <h2>Edit {{ $subtask->title }}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url('/dashboard') }}">Dashboard</a>
            </li>
            <li>
                <a href="{{ action('TemplateController@index') }}">Templates</a>
            </li>
            <li>
                <a href="{{ action('TemplateController@show', $subtask->template) }}">{{ $subtask->template->title }}</a>
            </li>
            <li>
                <a href="{{ action('TemplateController@show', $subtask->template) }}">Subtasks</a>
            </li>
            <li>
                <a href="{{ action('TemplateController@show', $subtask->template) }}">{{ $subtask->title }}</a>
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
        <form id="form" enctype="multipart/form-data" class="form-horizontal" role="form" method="POST" action="{{ action('TemplateSubtaskController@update', $subtask) }}">
            {{ csrf_field() }}
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Edit {{ $subtask->title }} <small>In {{ $subtask->template->title }}.</small></h5>
                </div>
                <div class="ibox-content">

                    {{-- Title --}}
                    <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                        <label class="col-sm-2 control-label" for="title">Title</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="title" id="title" value="{{ $subtask->title }}" required autofocus>

                            @if ($errors->has('title'))
                                <span class="help-block m-b-none">
                                    <strong>{{ $errors->first('title') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    {{-- Description --}}
                    <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                        <label class="col-sm-2 control-label" for="description">Description</label>

                        <div class="col-sm-10">
                            <textarea class="wysiwyg" name="description" id="description">{!! $subtask->versions->first()->description !!}</textarea>
                            @if ($errors->has('description'))
                                <span class="help-block m-b-none">
                                    <strong>{{ $errors->first('description') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Create a new revision --}}
                    <div class="form-group{{ $errors->has('version') ? ' has-error' : '' }}">
                        <label class="col-sm-2 control-label" for="version">Create new revision/version</label>

                        <div class="col-sm-10">
                            <input type="hidden" name="version" value="0" />
                            <input type="checkbox" class="js-switch" id="version" value="1" name="version" />
                            <span class="m-l-lg">Creating a new revision will force users to accept changes before completing the subtask</span>

                            @if ($errors->has('version'))
                                <span class="help-block m-b-none">
                                    <strong>{{ $errors->first('version') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @foreach($modules as $module)
                <div class="ibox float-e-margins subtask-template-module">
                    <input type="hidden" name="modules[{{$module->id}}][id]" value="{{$module->id}}" />
                    <div class="ibox-title">
                        <h5>{{$module->name}}</h5>
                        <div class="ibox-tools">
                            <input type="checkbox" data-size="small" class="js-switch js-switch-small" value="on" name="modules[{{$module->id}}][active]" {{in_array($module->id, $activeModules) ? 'checked' : ''}}>
                        </div>
                    </div>
                    <div class="ibox-content" style="{{in_array($module->id, $activeModules) ? '' : "display:none"}}">
                        <div class="desc">
                            <div>{{$module->description}}</div>
                            <div class="hr-line-dashed"></div>
                            <div>
                                @include('templates.subtasks.modules.'.$module->template.'.index', ['module'=>$module, 'settings'=>$modulesTemplateSettings[$module->id]] )
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <div class="desc">
                        <div class="form-group m-b-none">
                            <div class="col-sm-4">
                                <a class="btn btn-white" href="{{ action('TemplateController@show', $subtask->template) }}">Cancel</a>
                                <button class="btn btn-primary" type="submit">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            </form>

            {{-- Delete subtask form --}}
            @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                <div class="ibox float-e-margins">
                    <div class="ibox-title"><h5>Danger zone</h5></div>
                    <div class="ibox-content">
                        <a class="btn btn-danger btn-outline" href="{{ action('TemplateSubtaskController@showDeactivationSettings', $subtask) }}">Deactivate subtask</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function() {
            $(document).on('change', ".subtask-template-module .ibox-tools .js-switch", function() {
                var moduleContentContainer = $(this).closest(".subtask-template-module").find(".ibox-content");
                if( $(this).is(':checked') ) {
                    moduleContentContainer.slideDown();
                }
                else {
                    moduleContentContainer.slideUp();
                }
            });
        });
    </script>
@append
