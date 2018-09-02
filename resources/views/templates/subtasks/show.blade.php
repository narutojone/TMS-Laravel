@extends('layouts.app')

@section('title', $subtask->title)

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>{{ $subtask->title }}</h2>
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
                <li class="active">
                    <strong>{{ $subtask->title }}</strong>
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
                                    <h2>{{ $subtask->title }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="row">
                            <div class="col-lg-2">Current revision no.</div>
                            <div class="col-lg-10">
                                {{ $subtask->versions->first()->version_no }}
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        {{-- Description --}}
                        <div class="row">
                            <div class="col-lg-2">Description</div>
                            <div class="col-lg-10">
                                {!!  $subtask->versions->first()->description !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
