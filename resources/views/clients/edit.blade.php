@extends('layouts.app')

@section('title', 'Edit ' . $client->name)

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <h2>Edit {{ $client->name }}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url('/dashboard') }}">Dashboard</a>
            </li>
            <li>
                <a href="{{ action('ClientController@index') }}">Clients</a>
            </li>
            <li>
                <a href="{{ action('ClientController@show', $client) }}">{{ $client->name }}</a>
            </li>
            <li class="active">
                <strong>Edit</strong>
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
                    <h5>Edit client</h5>
                </div>
                <div class="ibox-content">
                    <form class="form-horizontal" role="form" method="POST" action="{{ action('ClientController@update', $client) }}">
                        {{ csrf_field() }}
                        @if(Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN) ||
                        (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_CUSTOMER_SERVICE) && !$client->internal) ||
                        (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_CUSTOMER_SERVICE) && Auth::id() === $client->employee_id))
                            {{-- Name --}}
                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="name">Name</label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="name" id="name" value="{{ $client->name }}" required autofocus>

                                    @if ($errors->has('name'))
                                        <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            {{-- Organization number --}}
                            <div class="form-group{{ $errors->has('organization_number') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="organization_number">Organization Number</label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="organization_number" id="organization_number" value="{{ $client->organization_number }}">

                                    @if ($errors->has('organization_number'))
                                        <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('organization_number') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Systems --}}
                            <div class="form-group{{ $errors->has('system_id') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="system_id">Software</label>

                                <div class="col-sm-10">
                                    <select class="form-control chosen-select" name="system_id" id="system_id">
                                        @foreach (\App\Repositories\System\System::visible()->get() as $system)
                                            <option value="{{ $system->id }}"{{ (old('system_id', $client->system_id) == $system->id) ? ' selected' : '' }}>{{ $system->name }}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('system_id'))
                                        <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('system_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="hr-line-dashed"></div>
                            {{-- Manager --}}

                            <div id="manager-wrapper">
                                <div class="form-group{{ $errors->has('manager_id') ? ' has-error' : '' }}">
                                    <label class="col-sm-2 control-label" for="manager_id">Manager</label>

                                    <div class="col-sm-10">
                                        <select class="form-control chosen-select" name="manager_id" id="manager_id">
                                            @foreach ($activeUsers as $user)
                                                <option value="{{ $user->id }}"{{ (old('manager_id') == $user->id) ? ' selected' : (($user->id == $lastManagerId) ? ' selected' : '') }}>{{ $user->name }}</option>
                                            @endforeach
                                        </select>

                                        @if ($errors->has('manager_id'))
                                            <span class="help-block m-b-none">
                                                <strong>{{ $errors->first('manager_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                @if($client->active == 1 && $client->paused == 0)
                                    <div class="form-group" v-show="shouldShowManagerMoveTypeField">
                                        <label class="col-sm-2 control-label" for="manager_move_rating">Manager Move Type</label>
                                        <div class="col-sm-10">
                                            <select class="form-control" id="manager_move_rating" name="manager_move_rating" v-model="manager_move_rating">
                                                <option value="null">Neutral</option>
                                                <option value="1">Positive</option>
                                                <option value="0">Negative</option>
                                            </select>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Employee --}}
                            <div id="employee-wrapper">
                                <div class="form-group{{ $errors->has('employee_id') ? ' has-error' : '' }}">
                                    <label class="col-sm-2 control-label" for="employee_id">Employee</label>

                                    <div class="col-sm-10">
                                        <select class="form-control chosen-select" name="employee_id" id="employee_id">
                                            @foreach ($activeUsers as $user)
                                                <option value="{{ $user->id }}"{{ (old('employee_id') == $user->id) ? ' selected' : (($user->id == $lastEmployeeId) ? ' selected' : '') }}>{{ $user->name }}</option>
                                            @endforeach
                                        </select>

                                        @if ($errors->has('employee_id'))
                                            <span class="help-block m-b-none">
                                                <strong>{{ $errors->first('employee_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                @if($client->active == 1 && $client->paused == 0)
                                    <div class="form-group" v-show="shouldShowEmployeeMoveTypeField">
                                        <label class="col-sm-2 control-label" for="employee_move_rating">Employee Move Type</label>
                                        <div class="col-sm-10">
                                            <select class="form-control" id="employee_move_rating" name="employee_move_rating" v-model="employee_move_rating">
                                                <option value="null">Neutral</option>
                                                <option value="1">Positive</option>
                                                <option value="0">Negative</option>
                                            </select>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="hr-line-dashed"></div>

                            {{-- Paid --}}
                            <div class="form-group{{ $errors->has('paid') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="paid">Paid</label>

                                <div class="col-sm-10">
                                    <input type="hidden" name="paid" value="0" />
                                    <input type="checkbox" class="js-switch" id="paid" value="1" name="paid" {{$client->paid ? 'checked' : ''}}>

                                    @if ($errors->has('paid'))
                                        <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('paid') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>{{-- Manager --}}

                            <div class="hr-line-dashed"></div>

                            {{-- Active --}}
                            <div class="form-group{{ $errors->has('active') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="active">Active</label>

                                <div class="col-sm-10">
                                    <input type="hidden" name="active" value="0" />
                                    <input type="checkbox" class="js-switch" id="active" name="active" value="1" {{$client->active ? 'checked' : ''}}>

                                    @if ($errors->has('active'))
                                        <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('active') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>

                            {{-- Paused --}}
                            <div class="form-group{{ $errors->has('paused') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="paused">Paused</label>

                                <div class="col-sm-10">
                                    <input type="hidden" name="paused" value="0" />
                                    <input type="checkbox" class="js-switch" id="paused" name="paused" value="1" {{$client->paused ? 'checked' : ''}}>

                                    @if ($errors->has('active'))
                                        <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('paused') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>

                            {{-- Client Folders --}}
                            <div class="form-group{{ $errors->has('show_folders') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="active">Show folders</label>

                                <div class="col-sm-10">
                                    <input type="hidden" name="show_folders" value="0" />
                                    <input type="checkbox" class="js-switch" value="1" id="show_folders" name="show_folders" {{$client->show_folders ?  'checked' : ''}}>

                                    @if ($errors->has('show_folders'))
                                        <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('show_folders') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>

                            {{-- Internal Client --}}
                            <div class="form-group{{ $errors->has('internal') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="internal">Internal Client</label>

                                <div class="col-sm-10">
                                    <input type="hidden" name="internal" value="0" />
                                    <input type="checkbox" class="js-switch" value="1" id="internal" name="internal" {{$client->internal ?  'checked' : ''}}>

                                    @if ($errors->has('internal'))
                                        <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('internal') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Harvest ID --}}
                            <div class="form-group{{ $errors->has('harvest_id') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="internal">Harvest ID</label>

                                <div class="col-sm-10">
                                    <input class="form-control" min="0" step="1" type="number" name="harvest_id" id="harvest_id" value="{{ $client->harvest_id }}">

                                    @if ($errors->has('harvest_id'))
                                        <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('harvest_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="hr-line-dashed"></div>

                        <div class="form-group m-b-none">
                            <div class="col-sm-4 col-sm-offset-2">
                                <a class="btn btn-white" href="{{ action('ClientController@show', $client) }}">Cancel</a>
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

@section('script')
    <script>
        new Vue({
            el: '#employee-wrapper',
            data: {
                employee: {
                    old: "{{ $client->employee_id }}"
                },
                employee_move_rating: null,
                shouldShowEmployeeMoveTypeField: false
            },
            methods: {
                shouldShowEmployeeMoveTypeField: function () {
                    this.shouldShowEmployeeMoveTypeField = true;
                }
            },
            mounted: function () {
                var that = this;
                $("#employee_id").chosen().change(function (e) {
                    var selected = $(e.target).val();
                    if (that.employee.old != selected) {
                        that.shouldShowEmployeeMoveTypeField = true;
                    } else {
                        that.shouldShowEmployeeMoveTypeField = false;
                    }
                });
            }
        });

        new Vue({
            el: '#manager-wrapper',
            data: {
                manager: {
                    old: "{{ $client->manager_id }}"
                },
                manager_move_rating: null,
                shouldShowManagerMoveTypeField: false
            },
            methods: {
                shouldShowManagerMoveTypeField: function () {
                    this.shouldShowManagerMoveTypeField = true;
                }
            },
            mounted: function () {
                var th = this;
                $("#manager_id").chosen().change(function (e) {
                    var selected = $(e.target).val();
                    if (th.manager.old != selected) {
                        th.shouldShowManagerMoveTypeField = true;
                    } else {
                        th.shouldShowManagerMoveTypeField = false;
                    }
                });
            }
        });
    </script>
@stop
