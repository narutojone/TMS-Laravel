@extends('layouts.app')

@section('title', 'Users Flagging')

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>User Flagging</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url('/dashboard') }}">Dashboard</a>
            </li>
            <li class="active">
                <strong>User Flagging</strong>
            </li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="wrapper wrapper-content">

            {{-- Users --}}
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Flags</h5>
                    <div class="ibox-tools">
                        @if(Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                            <a href="{{ route('settings.flags.create') }}" class="btn btn-primary btn-xs">Add flag</a>
                        @endif
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="project-list">
                        <table class="table table-hover">
                            <tbody>
                                @forelse($flags as $flag)
                                    <tr>
                                        <td>@include('flag-user.flagged', ['color' => $flag->hex])</td>
                                        <td class="project-title">
                                            {{ $flag->reason }}
                                        </td>
                                        <td class="project-list">
                                            {{ $flag->days or 'Endless' }}
                                        </td>
                                        <td class="project-actions">
                                            @if(Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                                                <a href="{{ route('settings.flags.edit', $flag) }}" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i> Edit</a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <span class="help-block"><em>no items...</em></span>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="text-center">
                            {{ $flags->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
