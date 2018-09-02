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
                    @if (auth()->user()->isAdminOrCustomerService())
                        <a href="{{ route('settings.information.index') }}">Users information</a>
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
                                    @if (auth()->user()->isAdmin())
                                        <a href="{{ route('settings.information.edit', $information) }}" class="btn btn-white btn-xs pull-right">Edit users information</a>
                                    @endif
                                    <h2>{{ $information->title }}</h2>
                                </div>
                            </div>
                        </div>

                        @if (auth()->user()->isAdmin())
                            <div class="row">
                                <div class="col-lg-5">
                                    <dl class="dl-horizontal m-b-none">
                                        <dt>Visibility:</dt> <dd>{{ implode(', ', $information->visibility) }}</dd>
                                    </dl>
                                </div>
                                <div class="col-lg-7">
                                    <dl class="dl-horizontal m-b-none">
                                        <dt>ID:</dt> <dd>{{ $information->id }}</dd>
                                    </dl>
                                </div>
                            </div>
                        @endif
                        <div class="hr-line-dashed"></div>

                        <div class="row">
                            <div class="col-lg-12">
                                @include('quill.view', ['delta' => $information->description])
                            </div>
                        </div>
                        @if (auth()->user()->isAdmin())
                        <div class="hr-line-dashed"></div>

                        <div class="row">
                            <div class="col-lg-12">

                                <h3 id="users">Responded users</h3>

                                @if ($information->users()->count() > 0)
                                    <div class="table-responsive m-t">
                                        <table class="table table-hover issue-tracker">
                                            <tbody>
                                                @foreach ($information->users as $user)
                                                    <tr>
                                                        <td>
                                                            {{ $user->name }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                    </div>
                                @else
                                    <i class="text-muted">no users</i>
                                @endif
                            </div>
                        </div>
                            <div class="hr-line-dashed"></div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <h3>Danger zone</h3>
                                    <form action="{{ route('settings.information.destroy', $information) }}" method="POST">
                                        {{ csrf_field() }}
                                        {{ method_field('delete') }}
                                        <button class="btn btn-primary">Delete</button>
                                    </form>
                                    <span class="help-block"><small>Users information will be deleted. Click responsible.</small></span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
