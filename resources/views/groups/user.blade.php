@extends('layouts.app')

@section('title', 'Add user to group')

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <h2>Add user to group</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url('/dashboard') }}">Dashboard</a>
            </li>
            <li>
                <a href="{{ route('groups.index') }}">Groups</a>
            </li>
            <li>
                <a href="{{ route('groups.show', $group) }}">{{ $group->name }}</a>
            </li>
            <li class="active">
                <strong>Add</strong>
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
                    <h5>Add user <small>To {{ $group->name }} group.</small></h5>
                </div>
                <div class="ibox-content">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('groups.users', $group) }}">
                        {{ csrf_field() }}

                        {{-- User --}}
                        <div class="form-group{{ $errors->has('user') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="user">User</label>

                            <div class="col-sm-10">
                                <select class="form-control chosen-select" name="user" id="user">
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"{{ (old('user') == $user->id) ? ' selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('user'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('user') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group m-b-none">
                            <div class="col-sm-4 col-sm-offset-2">
                                <a class="btn btn-white" href="{{ route('groups.show', $group) }}">Cancel</a>
                                <button class="btn btn-primary" type="submit">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
