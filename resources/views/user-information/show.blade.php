@extends('layouts.app')

@section('title', $information->title)

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>{{ $information->title }}</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                    @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                        <a href="{{ route('information.index') }}">Users information</a>
                    @else
                        Users information
                    @endif
                </li>
                <li class="active">
                    <strong>{{ $information->title }}</strong>
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
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="m-b-md">
                                    <h2>{{ $information->title }}</h2>
                                </div>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="row">
                            <div class="col-lg-12">
                                @include('quill.view', ['delta' => $information->description])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
