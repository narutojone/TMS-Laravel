@extends('layouts.app')

@section('title', 'API Settings')

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>API Settings</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url('/dashboard') }}">Dashboard</a>
            </li>
            <li>
                <a href="{{ action('SettingsController@edit') }}">Settings</a>
            </li>
            <li class="active">
                <strong>API</strong>
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
                        <h5>API token</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="form-horizontal">

                            {{-- View token --}}
                            <div class="form-group">
                                <label class="col-sm-2 control-label">API token</label>

                                <div class="col-sm-10">
                                    <p class="form-control-static">{{ Auth::user()->api_token }}</p>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            {{-- Regenerate token --}}
                            <form method="POST" action="{{ action('ApiController@regenerate') }}">
                                {{ csrf_field() }}

                                <div class="form-group m-b-none">
                                    <div class="col-sm-4 col-sm-offset-2">
                                        <button class="btn btn-white" type="submit">Regenerate token</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
