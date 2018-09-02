@extends('layouts.app')

@section('title', 'Edit ' . $user->name)

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <h2>Edit {{ $user->name }}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url('/dashboard') }}">Dashboard</a>
            </li>
            <li>
                <a href="{{ action('UserController@index') }}">Users</a>
            </li>
            <li>
                <a href="{{ action('UserController@show', $user) }}">{{ $user->name }}</a>
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
                    <h5>Edit user</h5>
                </div>
                <div class="ibox-content">
                    <form class="form-horizontal" role="form" method="POST" action="{{ action('UserController@update', $user) }}">
                        {{ csrf_field() }}

                        {{-- Name --}}
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="name">Name</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="name" id="name" value="{{ $user->name }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        {{-- E-Mail Address --}}
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="email">E-Mail Address</label>

                            <div class="col-sm-10">
                                <input type="email" class="form-control" name="email" id="email" value="{{ $user->email }}">

                                @if ($errors->has('email'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        {{-- PF ID --}}
                        <div class="form-group{{ $errors->has('pf_id') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="pf_id">PF ID</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="pf_id" id="pf_id" value="{{ $user->pf_id }}">

                                @if ($errors->has('pf_id'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('pf_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        {{-- Phone Number --}}
                        <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="phone">Phone Number</label>
                            <div class="col-sm-10">
                                <div class="input-group m-b">
                                    <span class="input-group-addon">+</span>
                                    <input type="number" id="phone" name="phone" placeholder="Phone number" value="{{ $user->phone }}" class="form-control">
                                </div>

                                @if ($errors->has('phone'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        {{-- Level --}}
                        <div class="form-group{{ $errors->has('level') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="level">Level</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="level" id="level" value="{{ $user->level }}" required>

                                @if ($errors->has('level'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('level') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        {{-- User Groups --}}
                        <div class="form-group{{ $errors->has('task_types') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="task_types">User Groups</label>
                            <div class="col-sm-10">
                                @foreach($groups as $groupId=>$group)
                                    <div>
                                        <label>
                                            <input value="{{$groupId}}" name="groups[]" type="checkbox" {{(collect(old('groups'))->contains($groupId)) || in_array($groupId, $userGroups) ? 'checked' : ''}}> {{$group}}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Submit --}}
                        <div class="form-group m-b-none">
                            <div class="col-sm-4 col-sm-offset-2">
                                <a class="btn btn-white" href="{{ action('UserController@show', $user) }}">Cancel</a>
                                <button class="btn btn-primary" type="submit">Save</button>
                            </div>
                        </div>
                    </form>

                    @if ($user->id != Auth::user()->id)
                        <div class="hr-line-dashed"></div>

                        {{-- Activate --}}
                        @if (!$user->active)
                            <form class="form-horizontal" role="form" method="POST" action="{{ action('UserController@activate', $user) }}">
                                {{ csrf_field() }}

                                <div class="form-group m-b-none">
                                    <div class="col-sm-4 col-sm-offset-2">
                                        <button class="btn btn-outline btn-success" type="submit">Activate</button>
                                    </div>
                                </div>
                            </form>
                        @endif

                        {{-- Deactivate --}}
                        @if ($user->active)
                            <form class="form-horizontal" role="form" method="POST" action="{{ action('UserController@deactivate', $user) }}">
                                {{ csrf_field() }}

                                <div class="form-group m-b-none">
                                    <div class="col-sm-4 col-sm-offset-2">
                                        <button class="btn btn-outline btn-danger" type="submit">Deactivate</button>
                                    </div>
                                </div>
                            </form>
                        @endif
                    @endif
                </div>
            </div>

            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Workload</h5>
                </div>
                <div class="ibox-content">
                    <form class="form-horizontal" role="form" method="POST" action="{{ action('UserController@update', $user) }}">
                        {{ csrf_field() }}

                        {{-- Authorized --}}
                        <div class="form-group{{ $errors->has('authorized') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="authorized">Authorized</label>

                            <div class="col-sm-10">
                                <input type="checkbox" class="js-switch" id="authorized" name="authorized" {{$user->authorized ? 'checked' : ''}}>

                                @if ($errors->has('authorized'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('authorized') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Customer Capacity --}}
                        <div class="form-group{{ $errors->has('customer_capacity') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="customer_capacity">Customer Capacity</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="customer_capacity" id="customer_capacity" value="{{$user->customer_capacity}}" required>

                                @if ($errors->has('customer_capacity'))
                                    <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('customer_capacity') }}</strong>
                                        </span>
                                @endif
                            </div>
                        </div>

                        {{-- Weekly Hour Capacity --}}
                        <div class="form-group{{ $errors->has('weekly_capacity') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="weekly_capacity">Weekly Hour Capacity</label>

                            <div class="col-sm-10">
                                <input type="number" min="0" step="0.1" class="form-control" name="weekly_capacity" id="weekly_capacity" value="{{$user->weekly_capacity}}" required>

                                @if ($errors->has('weekly_capacity'))
                                    <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('weekly_capacity') }}</strong>
                                        </span>
                                @endif
                            </div>
                        </div>

                        @if(!$groupForYearlyStatements || ($groupForYearlyStatements && $user->isGroupMember($groupForYearlyStatements->id)))
                            {{-- Yearly Statement Capacity--}}
                            <div class="form-group{{ $errors->has('yearly_statement_capacity') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="yearly_statement_capacity">Yearly Statement Capacity</label>

                                <div class="col-sm-10">
                                    <input type="number" class="form-control" name="yearly_statement_capacity" id="yearly_statement_capacity" onkeypress="return event.charCode >= 48" min="0" value="{{ $user->authorized ? $user->yearly_statement_capacity : 0}}" required {{ !$user->authorized ? 'readonly' : ''}}>

                                    @if ($errors->has('yearly_statement_capacity'))
                                        <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('yearly_statement_capacity') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Current customers --}}
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="customer_capacity">Existing customers</label>
                            <div class="col-sm-10">
                                @php ( $customersCount = $user->clients->count() )
                                <input type="text" class="form-control" value="{{ $customersCount > 0 ? $customersCount : 0  }}" readonly>
                            </div>
                        </div>

                        {{-- Customer types --}}
                        <div class="form-group{{ $errors->has('customer_types') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="customer_types">Customer types</label>
                            <div class="col-sm-10">
                                @foreach($customerTypes as $customerTypeId=>$customerType)
                                    <?php
                                    $checked = false;
                                    foreach($user->customerTypes as $existingCustomerType){
                                        if($existingCustomerType->id == $customerTypeId) {$checked=true; break;}
                                    }
                                    ?>
                                    <div><label> <input value="{{$customerTypeId}}" name="customer_types[]" type="checkbox" {{$checked ? 'checked' : ''}}> {{$customerType}}</label></div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Systems --}}
                        <div class="form-group{{ $errors->has('systems') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="systems">Systems</label>
                            <div class="col-sm-10">
                                @foreach($systems as $systemId=>$system)
                                    <?php
                                        $checked = false;
                                        foreach($user->systems as $existingSystem){
                                            if($existingSystem->id == $systemId) {$checked=true; break;}
                                        }
                                    ?>
                                    <div><label> <input value="{{$systemId}}" name="systems[]" type="checkbox" {{$checked ? 'checked' : ''}}> {{$system}}</label></div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Type of tasks --}}
                        <div class="form-group{{ $errors->has('task_types') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="task_types">Type of tasks</label>
                            <div class="col-sm-10">
                                @foreach($taskTypes as $taskTypeId=>$taskType)
                                    <?php
                                    $checked = false;
                                    foreach($user->taskTypes as $existingTaskType){
                                        if($existingTaskType->id == $taskTypeId) {$checked=true; break;}
                                    }
                                    ?>
                                    <div><label> <input value="{{$taskTypeId}}" name="task_types[]" type="checkbox" {{$checked ? 'checked' : ''}}> {{$taskType}}</label></div>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group m-b-none">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button class="btn btn-primary" type="submit">Update</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Extra information</h5>
                </div>
                <div class="ibox-content">
                    <form class="form-horizontal" role="form" method="POST" action="{{ action('UserController@update', $user) }}">
                        {{ csrf_field() }}

                        {{-- Country --}}
                        <div class="form-group{{ $errors->has('country') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="country">Country</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="country" id="country" value="{{$user->country}}">

                                @if ($errors->has('country'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('country') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Experience --}}
                        <div class="form-group{{ $errors->has('experience') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="experience">Experience</label>

                            <div class="col-sm-10">
                                <input type="number" min="0" step="0.1" class="form-control" name="experience" id="experience" value="{{$user->experience}}">

                                @if ($errors->has('experience'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('experience') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>


                        {{-- Degree --}}
                        <div class="form-group{{ $errors->has('degree') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="degree">Degree</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="degree" id="degree" value="{{$user->degree}}">

                                @if ($errors->has('degree'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('degree') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>


                        {{-- Invoice Percentage --}}
                        <div class="form-group{{ $errors->has('invoice_percentage') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="invoice_percentage">Invoice Percentage</label>

                            <div class="col-sm-10">
                                <input type="number" min="0" max="100" step="0.01" class="form-control" name="invoice_percentage" id="invoice_percentage" value="{{$user->invoice_percentage}}">

                                @if ($errors->has('invoice_percentage'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('invoice_percentage') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group m-b-none">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button class="btn btn-primary" type="submit">Update</button>
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
        $('#authorized').on('change', function () {
            var $yearlyStatementCapacity = $('#yearly_statement_capacity');

            if ($(this).is(':checked')) {
                $yearlyStatementCapacity.removeAttr('readonly');
            } else {
               $yearlyStatementCapacity.attr('readonly', true); 
            }
        });
    </script>
@endsection