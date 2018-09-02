<div class="row">
    @if(! $client->internal)
        <!-- Clients -->
        <div class="col-lg-5">
            <dl class="dl-horizontal m-b-none">
                <dt>Activated:</dt>
                <dd>{{$client->active ? 'Yes' : 'No'}}</dd>

                <dt>Organization number:</dt>
                <dd>
                    @if ($client->organization_number != '')
                        {{ $client->organization_number }}
                    @else
                        <i class="text-muted">none</i>
                    @endif
                </dd>

                <dt>Software:</dt> <dd>{{ $client->system->name }}</dd>

                @php($clientContacts = $client->contacts->first())

                <dt>Email:</dt>
                @php($clientMainEmail = $client->email())
                @if($clientMainEmail)
                    <dd><a href="mailto:{{ $clientMainEmail }}" class="text-navy">{{ $clientMainEmail }}</a></dd>
                @else
                    <i class="text-muted">none</i>
                @endif

                <dt>Phone:</dt>
                @php($clientMainPhone = $client->phone())
                @if($clientMainPhone)
                    <dd><a href="tel:+{{ $clientMainPhone }}" class="text-navy">+{{ $clientMainPhone }}</a></dd>
                @else
                    <i class="text-muted">none</i>
                @endif
            </dl>
        </div>
        <div class="col-lg-7">
            <dl class="dl-horizontal m-b-none">
                <dt>Customer risk:</dt>
                <dd>
                    {{$client->risk ? 'High' : 'Normal'}}

                    @if($client->risk)
                        <a id='client-risk-note' data-content='{{htmlentities($client->risk_reason)}}' href="#"><i class="fa fa-question-circle" aria-hidden="true"></i></a>
                    @endif

                    {{--Everybody can change status from 'normal' to 'high'. Only admin users or PM can switch it back --}}
                    @if($client->risk == 0 || ($client->risk == 1 && (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN) || $client->manager_id == Auth::user()->id)))
                        - <a data-toggle="modal" class="text-info" data-target="#risk-edit-modal">
                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                            Change
                        </a>
                    @endif
                </dd>
                <dt>Last Updated:</dt> <dd>@date($client->updated_at)</dd>
                <dt>Created:</dt> <dd>@date($client->created_at)</dd>
                <dt>Manager:</dt> <dd>
                    @if ($client->manager)
                        @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                            <a href="{{ action('UserController@show', $client->manager) }}">{{ $client->manager->name }}</a>
                        @else
                            {{ $client->manager->name }}
                        @endif
                        @if($log = $managerLogs->first())
                            -
                            <a data-toggle="modal" data-target="#manager-logs-modal">
                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                Assigned manager log
                            </a>
                        @endif
                    @else
                        <i class="text-muted">no manager</i>
                        @if($log = $managerLogs->first())
                            -
                            <a data-toggle="modal" data-target="#manager-logs-modal">
                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                Assigned manager log
                            </a>
                        @endif
                    @endif
                </dd>
                <dt>Employee:</dt> <dd>
                    @if ($client->employee)
                        @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                            <a href="{{ action('UserController@show', $client->employee) }}">{{ $client->employee->name }}</a>
                        @else
                            {{ $client->employee->name }}
                        @endif
                        @if($log = $employeeLogs->first())
                            -
                            <a data-toggle="modal" data-target="#employee-logs-modal">
                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                Assigned employee log
                            </a>
                        @endif
                    @else
                        <i class="text-muted">no employee</i>
                        @if($log = $employeeLogs->first())
                            -
                            <a data-toggle="modal" data-target="#employee-logs-modal">
                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                Assigned employee log
                            </a>
                        @endif
                    @endif
                </dd>
            </dl>
        </div>
    @else
        <!-- Internal Projects -->
        <div class="col-lg-5">
            <dl class="dl-horizontal m-b-none">
                <dt>Activated:</dt>
                <dd>{{$client->active ? 'Yes' : 'No'}}</dd>

                @php($clientContacts = $client->contacts->first())

                <dt>Email:</dt>
                @php($clientMainEmail = $client->email())
                @if($clientMainEmail)
                    <dd><a href="mailto:{{ $clientMainEmail }}" class="text-navy">{{ $clientMainEmail }}</a></dd>
                @else
                    <i class="text-muted">none</i>
                @endif

                <dt>Phone</dt>
                @php($clientMainPhone = $client->phone())
                @if($clientMainPhone)
                    <dd><a href="tel:+{{ $clientMainPhone }}" class="text-navy">+{{ $clientMainPhone }}</a></dd>
                @else
                    <i class="text-muted">none</i>
                @endif

            </dl>
        </div>
        <div class="col-lg-7">
            <dl class="dl-horizontal m-b-none">
                <dt>Last Updated:</dt> <dd>@date($client->updated_at)</dd>
                <dt>Created:</dt> <dd>@date($client->created_at)</dd>
                <dt>Manager:</dt> <dd>
                    @if ($client->manager)
                        @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                            <a href="{{ action('UserController@show', $client->manager) }}">{{ $client->manager->name }}</a>
                        @else
                            {{ $client->manager->name }}
                        @endif
                    @else
                        <i class="text-muted">no manager</i>
                    @endif
                </dd>
            </dl>
        </div>   
    @endif
</div>

@include('clients.risk')
@if(! $client->internal)
    <!-- Manager Logs Modal -->
    <div id="manager-logs-modal" class="modal fade">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Manager Logs</h4>
                </div>
                <div class="modal-body">
                    @if ($managerLogs)
                        <table class="table table-hover">
                            <thead>
                            <th>Manager</th>
                            <th>Rating</th>
                            <th>Assigned At</th>
                            <th>Removed At</th>
                            </thead>
                            <tbody>
                            @foreach ($managerLogs as $log)
                                <tr>
                                    <td class="project-title">
                                        {{ $log->employee ? $log->employee->name : 'No manager' }}
                                        @if ($log->employee && !$log->employee->active)
                                            <span class="label label-default">Deactivated</span>
                                        @endif
                                    </td>
                                    <td>{{ $log->rating && $log->user_id ? $log->rating : '-'}}</td>
                                    <td>@if ($log->assigned_at) @date($log->assigned_at) @else - @endif</td>
                                    <td>@if ($log->removed_at) @date($log->removed_at) @else - @endif</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {!! $managerLogs->links() !!}
                    @else
                        <i class="text-muted">No logs...</i>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee Logs Modal -->
    <div id="employee-logs-modal" class="modal fade">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Employee Logs</h4>
                </div>
                <div class="modal-body">
                    @if ($employeeLogs)
                        <table class="table table-hover">
                            <thead>
                            <th>Employee</th>
                            <th>Rating</th>
                            <th>Assigned At</th>
                            <th>Removed At</th>
                            </thead>
                            <tbody>
                            @foreach ($employeeLogs as $log)
                                <tr>
                                    <td class="project-title">
                                        {{ $log->employee ? $log->employee->name : 'No employee' }}
                                        @if ($log->employee && !$log->employee->active)
                                            <span class="label label-default">Deactivated</span>
                                        @endif
                                    </td>
                                    <td>{{ $log->rating && $log->user_id ? $log->rating : '-'}}</td>
                                    <td>@if ($log->assigned_at) @date($log->assigned_at) @else - @endif</td>
                                    <td>@if ($log->removed_at) @date($log->removed_at) @else - @endif</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {!! $employeeLogs->links() !!}
                    @else
                        <i class="text-muted">No logs...</i>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN) || Auth::user()->hasRole(\App\Repositories\User\User::ROLE_CUSTOMER_SERVICE))
        {{-- Show all client contacts --}}
        <div class="hr-line-dashed"></div>
        <div class="row">
            <div class="col-lg-12">
                <h3 id="contacts">
                    <div class="pull-right">
                        @can('update', $client)
                            <a href="{{ route('client.contacts.index', $client) }}" class="btn btn-xs btn-primary m-l-lg">Manage contacts</a>
                        @endcan
                    </div>
                    Contact persons
                </h3>
                <table class="table table-hover">
                    @if ($client->contacts->count())
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>E-mail</th>
                            <th>Primary</th>
                        </tr>
                        </thead>
                    @endif
                    <tbody>
                    @forelse ($client->contacts as $contact)
                        @php ($phonesCount = count($contact->phones))
                        @php ($emailsCount = count($contact->emails))
                        <tr>
                            <td>{{ $contact->name }}</td>
                            <td>
                                @if($phonesCount)
                                    +{{ $contact->phones->first()->number }}
                                    @if($phonesCount > 1)
                                        <span class="label label-primary m-l-md">+{{ $phonesCount - 1 }} more</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($emailsCount)
                                    {{ $contact->emails->first()->address }}
                                    @if($emailsCount > 1)
                                        <span class="label label-primary m-l-md">+{{ $emailsCount - 1 }} more</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $contact->isPrimary() ? 'Yes' : 'No' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <span><em>no contact persons assigned...</em></span>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Show client contracts --}}
        <div class="hr-line-dashed"></div>
        <div class="row">
            <div class="col-lg-12">
                <h3 id="contracts">
                    <div class="pull-right">
                        @can('create', [App\Repositories\Contract\Contract::class, $client])
                            @if (Auth::user()->isAdmin())
                                <div class="btn-group">
                                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle">Add contract <span class="caret"></span></button>
                                    <ul class="dropdown-menu">
                                        <li><a href="{{ route('clients.contracts.create', ['client' => $client, 'type' => 'simple']) }}">Simple</a></li>
                                        <li><a href="{{ route('clients.contracts.create', ['client' => $client, 'type' => 'advanced']) }}">Advanced</a></li>
                                    </ul>
                                </div>
                            @else
                                <div class="btn-group">
                                    <a href="{{ route('clients.contracts.create', ['client' => $client, 'type' => 'simple']) }}" class="btn btn-xs btn-primary">Add contract</a>
                                </div>
                            @endif
                        @endcan
                    </div>
                    Contracts
                </h3>
                <table class="table table-hover">
                    @if ($client->contracts->count())
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Start date</th>
                            <th>End date</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                        </thead>
                    @endif
                    <tbody>
                    @forelse ($client->contracts as $contract)
                        <tr>
                            <td>#{{ $contract->id }}</td>
                            <td>{{ $contract->one_time ? 'One time contract' : 'Regular contract' }}</td>
                            <td>{{ $contract->start_date }}</td>
                            <td>{{ $contract->end_date ? $contract->end_date : 'n/a' }}</td>
                            <td>{!! $contract->active ? '<i class="fa fa-check text-navy"></i> Active' : '<i class="fa fa-times text-danger"></i> Inactive' !!}</td>
                            <td class="project-actions">
                                @can('show', App\Repositories\Contract\Contract::class)
                                    <a href="{{ route('contracts.show', $contract) }}" class="btn btn-xs btn-primary btn-outline">View contract</a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <span><em>no contracts created...</em></span>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endif

<div class="hr-line-dashed"></div>
<div class="row">
    <div class="col-lg-12">
        <h3 id="logs">
            Logs
        </h3>
        <table class="table table-hover">
            @if ($client->editLogs->count())
                <thead>
                <tr>
                    <th>Field</th>
                    <th>Value</th>
                    <th>Changed by</th>
                    <th>Start date</th>
                    <th>End date</th>
                    <th>Day count</th>
                    <th>Comment</th>
                </tr>
                </thead>
            @endif
            <tbody>
            @forelse ($client->editLogs as $log)
                <tr>
                    <td>{{ $log->field }}</td>
                    <td>{{ $log->value ? 'Yes' : 'No' }}</td>
                    <td>{{ $log->user ? $log->user->name : 'System' }}</td>
                    <td>{{ Carbon\Carbon::parse($log->starts_at)->format('d/m/Y H:i') }}</td>
                    <td>{{ $log->ends_at ? Carbon\Carbon::parse($log->ends_at)->format('d/m/Y H:i') : '-' }}</td>
                    <td>
                        @if($log->ends_at)
                            {{ Carbon\Carbon::parse($log->ends_at)->diffInDays(Carbon\Carbon::parse($log->starts_at)) }}
                        @else
                            {{ Carbon\Carbon::now()->diffInDays(Carbon\Carbon::parse($log->starts_at)) }}
                        @endif
                    </td>
                    <td>{{ $log->comment }}</td>
                </tr>
            @empty
                <tr>
                    <span><em>No logs available...</em></span>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
