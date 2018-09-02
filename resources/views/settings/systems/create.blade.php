@extends('layouts.app')

@section('title', 'Create system')

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Create system</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url('/dashboard') }}">Dashboard</a>
            </li>
            <li>
                <a href="{{ route('systems.index') }}">Systems</a>
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
                    <h5>Create new system</h5>
                </div>
                <div class="ibox-content">
                    <form id="form" class="form-horizontal" role="form" method="POST" action="{{ route('systems.store') }}">
                        {{ csrf_field() }}

                        {{-- Name --}}
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="name">Name</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        {{-- Visible --}}
                        <div class="form-group{{ $errors->has('visible') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="visible">Visible</label>

                            <div class="col-sm-10">
                                <input type="hidden" name="visible" value="0" />
                                <input type="checkbox" class="js-switch" id="visible" value="1" name="visible" {{ old('visible') == 1 ? 'checked' : ''}}>

                                @if ($errors->has('visible'))
                                    <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('visible') }}</strong>
                                        </span>
                                @endif
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        {{-- Default --}}
                        <div class="form-group{{ $errors->has('default') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="default">Default</label>

                            <div class="col-sm-10">
                                <input type="hidden" name="default" value="0" />
                                <input type="checkbox" class="js-switch" id="default" value="1" name="default" {{ old('default') == 1 ? 'checked' : ''}}>

                                @if ($errors->has('default'))
                                    <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('default') }}</strong>
                                        </span>
                                @endif
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group m-b-none">
                            <div class="col-sm-4 col-sm-offset-2">
                                <a class="btn btn-white" href="{{ route('systems.index') }}">Cancel</a>
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
