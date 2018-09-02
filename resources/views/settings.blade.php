@extends('layouts.app')

@section('title', 'Settings')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="wrapper wrapper-content">

                {{-- Personal details --}}
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Personal details</h5>
                        <a href="{{ route('users.ooo.create', Auth::user()) }}" class="btn btn-danger btn-xs pull-right">Set out of office</a>
                    </div>
                    <div class="ibox-content">
                        <div class="form-horizontal">

                            {{-- Name --}}
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Name</label>

                                <div class="col-sm-10">
                                    <p class="form-control-static">{{ Auth::user()->name }}</p>
                                </div>
                            </div>

                            {{-- E-Mail Address --}}
                            <div class="form-group">
                                <label class="col-sm-2 control-label">E-Mail Address</label>

                                <div class="col-sm-10">
                                    <p class="form-control-static">{{ Auth::user()->email }}</p>
                                </div>
                            </div>

                            {{-- Harvest ID --}}
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Harvest ID</label>

                                <div class="col-sm-10">
                                    <p class="form-control-static">{{ Auth::user()->harvest_id }}</p>
                                </div>
                            </div>

                            {{-- PF ID --}}
                            @if(Auth::user()->pf_id)
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">PF ID</label>

                                    <div class="col-sm-10">
                                        <p class="form-control-static">#{{ Auth::user()->pf_id }}</p>
                                    </div>
                                </div>
                            @endif

                            <div class="hr-line-dashed"></div>

                            <div class="form-group m-b-none">
                                <div class="col-sm-10 col-sm-offset-2">
                                    <span class="text-muted">These details can only be updated by an administrator.</span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Workload and accounting information</h5>
                </div>
                <div class="ibox-content">
                    <form class="form-horizontal" role="form" method="POST" action="{{ action('SettingsController@update') }}">
                        {{ csrf_field() }}
                        {{-- Authorized --}}
                        <div class="form-group{{ $errors->has('authorized') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="authorized">Authorized</label>

                            <div class="col-sm-10">
                                <input type="checkbox" class="js-switch" id="authorized" name="authorized" {{Auth::user()->authorized ? 'checked' : ''}}>

                                @if ($errors->has('authorized'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('authorized') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        {{-- Current customers --}}
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="customer_capacity">Existing customers</label>
                            <div class="col-sm-10">
                                @php ( $customersCount = Auth::user()->clients->count() )
                                <input type="text" class="form-control" value="{{ $customersCount }}" readonly>
                            </div>
                        </div>
                        
                        <div class="hr-line-dashed"></div>

                        {{-- Weekly Hour Capacity --}}
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="customer_capacity">Weekly Hour Capacity</label>
                            <div class="col-sm-10">
                                <input type="number" name="weekly_capacity" class="form-control" value="{{ Auth::user()->weekly_capacity }}" min="0" step="0.1">
                            </div>
                        </div>

                        {{-- Workload capacity --}}
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="">Monthly workload</label>
                            <div class="col-sm-10">
                                @foreach($workloadMonths as $workloadDate => $workloadMonth)
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <input type="number" name="workload[{{$workloadDate}}]" class="form-control" value="{{ $workloadMonth['hours'] }}" min="1" step="1" {{ $workloadMonth['locked'] ? 'disabled' : '' }}>
                                        </div>
                                        <div class="col-sm-4">
                                            <label class="m-t-xs">{{ \Carbon\Carbon::parse($workloadDate)->format('Y F') }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        @if(!$groupForYearlyStatements || ($groupForYearlyStatements && auth()->user()->isGroupMember($groupForYearlyStatements->id)))
                            {{-- Yearly Statement Capacity--}}
                            <div class="form-group{{ $errors->has('yearly_statement_capacity') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="yearly_statement_capacity">Yearly Statement Capacity</label>

                                <div class="col-sm-10">
                                    <input type="number" class="form-control" name="yearly_statement_capacity" id="yearly_statement_capacity" onkeypress="return event.charCode >= 48" min="1" value="{{Auth::user()->authorized ? Auth::user()->yearly_statement_capacity : 0}}" required {{ !Auth::user()->authorized ? 'readonly' : ''}}>

                                    @if ($errors->has('yearly_statement_capacity'))
                                        <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('yearly_statement_capacity') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                        <div class="hr-line-dashed"></div>

                        {{-- Customer types --}}
                        <div class="form-group{{ $errors->has('customer_types') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="customer_types">Customer types</label>
                            <div class="col-sm-10">
                                @foreach($customerTypes as $customerTypeId=>$customerType)
                                    <?php
                                        $checked = false;
                                        foreach(Auth::user()->customerTypes as $existingCustomerType){
                                            if($existingCustomerType->id == $customerTypeId) {$checked=true; break;}
                                        }
                                    ?>
                                    <div><label> <input value="{{$customerTypeId}}" name="customer_types[]" type="checkbox" {{$checked ? 'checked' : ''}}> {{$customerType}}</label></div>
                                @endforeach
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        {{-- Type of tasks --}}
                        <div class="form-group{{ $errors->has('task_types') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="task_types">Type of tasks</label>
                            <div class="col-sm-10">
                                @foreach($taskTypes as $taskTypeId=>$taskType)
                                    <?php
                                        $checked = false;
                                        foreach(Auth::user()->taskTypes as $existingTaskType){
                                            if($existingTaskType->id == $taskTypeId) {$checked=true; break;}
                                        }
                                    ?>
                                    <div><label> <input value="{{$taskTypeId}}" name="task_types[]" type="checkbox" {{$checked ? 'checked' : ''}}> {{$taskType}}</label></div>
                                @endforeach
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        {{-- Systems --}}
                        <div class="form-group{{ $errors->has('systems') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="systems">Systems</label>
                            <div class="col-sm-10">
                                <p>Under finner du en liste med forskjellige regnskapssystemer. Huk av de programmene du kan <u><strong>veldig godt</strong></u> og som du ønsker kunder på. Om du ikke kan noen av systemene kan du starte med Fiken og heller utvide med flere systemer senere.</p>
                                @foreach($systems as $systemId=>$system)
                                    <?php
                                        $checked = false;
                                        foreach(Auth::user()->systems as $existingSystem){
                                            if($existingSystem->id == $systemId) {$checked=true; break;}
                                        }
                                    ?>
                                    <div><label> <input value="{{$systemId}}" name="systems[]" type="checkbox" {{$checked ? 'checked' : ''}}> {{$system}}</label></div>
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
                    <h5>Out of office log</h5>
                </div>
                <div class="ibox-content">
                    <h3 class="m-t-none m-b">Previous</h3>
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>From date</th>
                            <th>To date</th>
                            <th>Reason</th>
                            <th>Created at</th>
                            <th>&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                            @php ($now = \Carbon\Carbon::now()->toDateString())
                            @foreach($outOfOffice as $ooo)
                                @if($ooo->to_date < $now)
                                <tr>
                                    <td>{{ $ooo->id }}</td>
                                    <td>{{ $ooo->from_date }}</td>
                                    <td>{{ $ooo->to_date }}</td>
                                    <td>{{ $ooo->reason->name }}</td>
                                    <td class="text-navy">{{ $ooo->created_at }}</td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>

                    <h3 class="m-t-none m-b">Upcoming</h3>
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>From date</th>
                            <th>To date</th>
                            <th>Reason</th>
                            <th>Created at</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($outOfOffice as $ooo)
                                @if($ooo->from_date > $now)
                                    <tr>
                                        <td>{{ $ooo->id }}</td>
                                        <td>{{ $ooo->from_date }}</td>
                                        <td>{{ $ooo->to_date }}</td>
                                        <td>{{ $ooo->reason->name }}</td>
                                        <td class="text-navy">{{ $ooo->created_at }}</td>
                                        <td>
                                            <form class="form-horizontal" role="form" method="POST" action="{{ action('UserController@removeOoo', Auth::user()) }}">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="ooo" value="{{ $ooo->id }}" />
                                                <input type="submit" value="Remove" class="btn btn-danger btn-xs btn-outline" />
                                            </form>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Flags
            <div class="ibox float-e-margins">
                <div class="ibox-title"><h5>Flag log</h5></div>
                <div class="ibox-content">
                    <table class="table table-hover">
                        @if ($flags->count())
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Flag title</th>
                                    <th>Flag date</th>
                                    <th>Valid To</th>
                                    <th>Client</th>
                                    <th>Comment</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        @endif
                        <tbody>
                            @forelse ($flags as $flag)
                                <tr>
                                    <td>@include('flag-user.flagged', ['color' => $flag->hex])</td>
                                    <td class="project-title">{{ $flag->reason }}</td>
                                    <td>{{ Carbon\Carbon::parse($flag->pivot->created_at)->format('Y-m-d') }}</td>
                                    <td>{{ $flag->validTo() }}</td>
                                    <td>
                                        @if($flag->pivot->client_id)
                                            {{ App\Repositories\Client\Client::find($flag->pivot->client_id)->name }}
                                        @else
                                           N/A
                                        @endif
                                    </td>
                                    <td>{{ $flag->pivot->comment }}</td>
                                    <td>
                                        @if ($flag->pivot->active == 1)
                                            <span class="label label-success">Active</span>
                                        @else
                                            <span class="label label-default">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><span><em>no flags...</em></span></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            --}}
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
