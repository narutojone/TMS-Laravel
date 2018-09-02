@extends('layouts.app')

@section('title', 'TMS General Settings')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2>Option Settings</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li class="active">
                    <strong>TMS General Settings</strong>
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
                        <h5>All options</h5>
                    </div>
                    <div class="ibox-content">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#ID</th>
                                    <th>Name</th>
                                    <th>Value</th>
                                    <th>Description</th>
                                    <th class="project-actions">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($options as $option)
                                <tr>
                                    <form method="POST" action="{{ route('settings.options.update', $option) }}">
                                        {{ csrf_field() }}
                                        <td>{{ $option->id }}</td>
                                        <td>{{ $option->name }}</td>
                                        <td>
                                            <div class="" style="width:300px;">
                                                @if(isset($specialFields[$option->key]) && $specialFields[$option->key] == 'emailTemplates')
                                                    <select class="form-control chosen-select" name="value" id="{{ $option->key }}">
                                                        @foreach ($emailTemplates as $emailTemplate)
                                                            <option value="{{ $emailTemplate->id }}" {{ $option->value == $emailTemplate->id ? 'selected' : '' }}>{{ $emailTemplate->name }}</option>
                                                        @endforeach
                                                    </select>
                                                @elseif(isset($specialFields[$option->key]) && $specialFields[$option->key] == 'overdueReasons')
                                                    <select class="form-control chosen-select" name="value" id="{{ $option->key }}">
                                                        @foreach ($overdueReasons as $overdueReason)
                                                            <option value="{{ $overdueReason->id }}" {{ $option->value == $overdueReason->id ? 'selected' : '' }}>{{ $overdueReason->reason }}</option>
                                                        @endforeach
                                                    </select>
                                                @elseif(isset($specialFields[$option->key]) && $specialFields[$option->key] == 'taskTemplates')
                                                    <select class="form-control chosen-select" name="value" id="{{ $option->key }}">
                                                        @foreach ($taskTemplates as $taskTemplate)
                                                            <option value="{{ $taskTemplate->id }}" {{ $option->value == $taskTemplate->id ? 'selected' : '' }}>{{ $taskTemplate->title }}</option>
                                                        @endforeach
                                                    </select>
                                                @elseif(isset($specialFields[$option->key]) && $specialFields[$option->key] == 'groups')
                                                    <select class="form-control chosen-select" name="value" id="{{ $option->key }}">
                                                        @foreach ($groups as $group)
                                                            <option value="{{ $group->id }}" {{ $option->value == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <input type="text" class="form-control input-sm" name="value" id="{{ $option->key }}" value="{{ $option->value }}" />
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $option->description }}</td>
                                        <td class="project-actions">
                                            <input type="submit" name="submit" class="btn btn-primary btn-sm" value="Update"/>
                                        </td>
                                    </form>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
