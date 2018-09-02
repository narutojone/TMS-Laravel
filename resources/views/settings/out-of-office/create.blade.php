@extends('layouts.app')

@section('title', 'Create out of office reason')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2>Create out of office reason</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                    <a href="{{ route('settings.ooo.index') }}">Out of office reasons</a>
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
                    <div class="ibox-title"><h5>Create out of office reason</h5></div>
                    <div class="ibox-content">
                        <form class="form-horizontal" role="form" method="POST" action="{{ action('OooController@store') }}">
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

                            {{-- Default --}}
                            <div class="form-group{{ $errors->has('default') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="default">Default</label>

                                <div class="col-sm-10">
                                    <input type="checkbox" class="form-control" name="default" id="default" value="{{ App\Repositories\OooReason\OooReason::DEFAULT  }}">

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
                                    <a class="btn btn-white" href="{{ action('OooController@index') }}">Cancel</a>
                                    <button class="btn btn-primary" type="submit">Create</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
