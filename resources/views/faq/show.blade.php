@extends('layouts.app')

@section('title', $faq->title)

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>{{ $faq->title }}</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                    <li>
                        <a href="{{ action('FaqCategoryController@index') }}">Faq Categories</a>
                    </li>
                @endif
                <li>
                    <a href="{{ action('FaqCategoryController@show', $faq->faqCategory) }}">{{ $faq->faqCategory->name }}</a>
                </li>
                <li class="active">
                    <strong>{{ $faq->title }}</strong>
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
                                    <div class="pull-right">
                                        @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                                            <a href="{{ action('FaqController@edit', $faq) }}" class="btn btn-white btn-xs">Edit</a>
                                        @endif
                                    </div>
                                    <h2>{{ $faq->title }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        {{-- Description --}}
                        <div class="row">
                            <div class="col-lg-12">
                                {!! $faq->content !!}
                            </div>
                        </div>
                        @if($faq->tasks() != NULL)
                            @foreach ($faq->tasks() as $subFaq)
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="ibox">
                                            <div class="ibox-title">
                                                <a class="collapse-link">
                                                    <h4>{{ $subFaq->title }} <i class="fa fa-chevron-down"></i></h4>
                                                </a>
                                            </div>
                                            <div class="ibox-content" style="display: none;">
                                                {!! $subFaq->versions->first()->description !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection