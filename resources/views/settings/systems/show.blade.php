@extends('layouts.app')

@section('title', "System {$system->name}")

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>{{ $system->name }}</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                    @if (auth()->user()->isAdminOrCustomerService())
                        <a href="{{ route('systems.index') }}">Systems</a>
                    @else
                        Systems
                    @endif
                </li>
                <li class="active">
                    <strong>{{ $system->name }}</strong>
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
                                <div class="m-b-md clearfix">
                                    @if (auth()->user()->isAdmin())
                                        <a href="{{ route('systems.edit', $system) }}" class="btn btn-white btn-xs pull-right">Edit system</a>
                                        <form style="margin-right: 5px" class="pull-right" action="{{ route('systems.destroy', $system) }}" method="POST">
                                            {{ csrf_field() }}
                                            {{ method_field('delete') }}
                                            <button class="btn btn-xs btn-danger">Remove system</button>
                                        </form>
                                    @endif
                                    <h2>{{ $system->name }} system</h2>
                                </div>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="m-b-md">
                                    <form id="form" class="form-horizontal">
                                        {{ csrf_field() }}
                                        {{-- Name --}}
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label" for="name">Name</label>

                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="name" id="name" value="{{  $system->name }}" required autofocus disabled>
                                            </div>
                                        </div>

                                        <div class="hr-line-dashed"></div>

                                        {{-- Visible --}}
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label" for="visible">Visible</label>

                                            <div class="col-sm-10">
                                                <input type="checkbox" class="js-switch" id="visible" value="1" name="visible" {{ $system->visible == 1 ? 'checked' : ''}} disabled>
                                            </div>
                                        </div>

                                        <div class="hr-line-dashed"></div>

                                        {{-- Default --}}
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label" for="default">Default</label>

                                            <div class="col-sm-10">
                                                <input type="checkbox" class="js-switch" id="default" value="1" name="default" {{ $system->default == 1 ? 'checked' : ''}} disabled>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
