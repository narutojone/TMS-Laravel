@extends('layouts.app')

@section('title', 'Templates')

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Templates</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url('/dashboard') }}">Dashboard</a>
            </li>
            <li class="active">
                <strong>Templates</strong>
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
                <div class="ibox-title">
                    <h5>All templates</h5>

                    @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                        <div class="ibox-tools">
                            <a href="{{ action('TemplateController@create') }}" class="btn btn-primary btn-xs">Create new template</a>
                        </div>
                    @endif
                </div>
                <div class="ibox-content">
                    <form role="form" class="form-inline" method="get" action="">
                        {{-- Category filter --}}
                        <label class="control-label m-r-xs" for="category">Category</label>

                        <select class="form-control chosen-select" name="category" id="category" style="min-width: 160px;">
                            <option></option>
                            @foreach ($categories as $category)
                                <option{{ ($selectedCategory == $category) ? ' selected' : '' }}>{{ $category }}</option>
                            @endforeach
                        </select>

                        {{-- Search --}}
                        <label class="control-label m-l-md m-r-xs" for="search">Search</label>

                        <input class="form-control" type="text" name="search" id="search" value="{{ $currentSearch }}">

                        <button class="btn btn-primary m-l-md" type="submit">Filter</button>
                    </form>
                    <div class="hr-line-dashed"></div>

                    @if ($templates->count() > 0)
                        <div class="project-list">
                            <table class="table table-hover">
                                <tbody>
                                    @foreach ($templates as $template)
                                        <tr>
                                            <td class="project-title">
                                                <a href="{{ action('TemplateController@show', $template) }}">{{ $template->title }}</a>
                                                <br>
                                                <small>{{ $template->category }}</small>
                                            </td>
                                            <td class="project-actions">
                                                <a href="{{ action('TemplateController@show', $template) }}" class="btn btn-white btn-sm">
                                                    <i class="fa fa-folder"></i> View
                                                </a>
                                                @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                                                    <a href="{{ action('TemplateController@edit', $template) }}" class="btn btn-white btn-sm">
                                                        <i class="fa fa-pencil"></i> Edit
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="text-center">
                                {{ $templates->links() }}
                            </div>
                        </div>
                    @else
                        <i class="text-muted">no templates</i>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
