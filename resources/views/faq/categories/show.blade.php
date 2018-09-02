@extends('layouts.app')

@section('title', $faqCategory->name)

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>{{ $faqCategory->name }}</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                    <li>
                        <a href="{{ action('FaqCategoryController@index') }}">FAQ Categories</a>
                    </li>
                @endif
                <li class="active">
                    <strong>{{ $faqCategory->name }}</strong>
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
                                    @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                                        <a href="{{ action('FaqCategoryController@edit', $faqCategory) }}" class="btn btn-primary btn-xs pull-right">Edit FAQ Category</a>
                                        <a href="{{ action('FaqController@create', ['faqCategory' => $faqCategory]) }}" class="btn btn-primary btn-xs pull-right" style="margin-right: 10px;">Add FAQ</a>
                                    @endif
                                    <h2>{{ $faqCategory->name }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">

                                <div class="table-responsive m-t">
                                    <table class="table table-hover issue-tracker">
                                        <tbody>
                                        @forelse ($faqs as $faq)
                                            <tr>
                                                <td style="width: 60px;">
                                                    <form role="form" method="POST" action="{{ action('FaqController@move', $faq) }}">
                                                        {{ csrf_field() }}
                                                        {{--Up--}}
                                                        @if ($faq->order > 1)
                                                            <button type="submit" name="direction" value="up" class="btn btn-xs btn-default">&uarr;</button>
                                                        @else
                                                            <button class="btn btn-xs btn-default" disabled>&uarr;</button>
                                                        @endif

                                                        {{--Down--}}
                                                        @if ($faq->order < $faq->count())
                                                            <button type="submit" name="direction" value="down" class="btn btn-xs btn-default">&darr;</button>
                                                        @else
                                                            <button class="btn btn-xs btn-default" disabled>&darr;</button>
                                                        @endif
                                                    </form>
                                                </td>
                                                <td class="project-title">
                                                    <a href="{{ action('FaqController@show', $faq) }}">{{ $faq->title }}</a>
                                                </td>
                                                @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                                                    <td>{{ $faq->visible ? 'Visible' : ''}}</td>
                                                    <td>
                                                        @if( ! $faq->active)
                                                            <span class="label label-danger">Deactivated</span>
                                                        @endif
                                                    </td>
                                                @endif
                                                <td class="project-actions">
                                                    <a href="{{ action('FaqController@show', $faq) }}" class="btn btn-white btn-sm">
                                                        <i class="fa fa-folder"></i> View
                                                    </a>
                                                    @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                                                        <a href="{{ action('FaqController@edit', $faq) }}" class="btn btn-white btn-sm">
                                                            <i class="fa fa-pencil"></i> Edit
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <i class="text-muted">no faq</i>
                                        @endforelse
                                        </tbody>
                                    </table>

                                    <div class="text-center">
                                        {{ $faqs->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection