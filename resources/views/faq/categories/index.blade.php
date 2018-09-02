@extends('layouts.app')

@section('title', 'FAQ Categories')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2>FAQ Categories</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li class="active">
                    <strong>FAQ Categories</strong>
                </li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="wrapper wrapper-content">

                {{-- Categories --}}
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>FAQ Categories</h5>
                        <div class="ibox-tools">
                            @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                                <a href="{{ action('FaqCategoryController@create') }}" class="btn btn-primary btn-xs pull-right">Add new category</a>
                                <a href="{{ action('FaqController@create') }}" class="btn btn-primary btn-xs pull-right" style="margin-right: 10px;">Add FAQ</a>
                            @endif
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="project-list">
                            <table class="table table-hover">
                                <tbody>
                                @foreach ($categories as $category)
                                    <tr>
                                        <td style="width: 60px;">
                                            <form role="form" method="POST" action="{{ action('FaqCategoryController@move', $category) }}">
                                                {{ csrf_field() }}

                                                {{--Up--}}
                                                @if ($category->order > 1)
                                                    <button type="submit" name="direction" value="up" class="btn btn-xs btn-default">&uarr;</button>
                                                @else
                                                    <button class="btn btn-xs btn-default" disabled>&uarr;</button>
                                                @endif

                                                {{--Down--}}
                                                @if ($category->order < $category->count())
                                                    <button type="submit" name="direction" value="down" class="btn btn-xs btn-default">&darr;</button>
                                                @else
                                                    <button class="btn btn-xs btn-default" disabled>&darr;</button>
                                                @endif
                                            </form>
                                        </td>
                                        <td class="project-title">
                                            <a href="{{ action('FaqCategoryController@show', $category) }}">{{ $category->name }}</a>
                                        </td>
                                        <td>{{ $category->visible ? 'Visible' : ''}}</td>
                                        <td>
                                            @if( ! $category->active)
                                                <span class="label label-danger">Deactivated</span>
                                            @endif
                                        </td>
                                        <td class="project-actions">
                                            <a href="{{ action('FaqCategoryController@show', $category) }}" class="btn btn-white btn-sm">
                                                <i class="fa fa-folder"></i> View
                                            </a>
                                            <a href="{{ action('FaqCategoryController@edit', $category) }}" class="btn btn-white btn-sm"><i class="fa fa fa-pencil"></i> Edit</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection