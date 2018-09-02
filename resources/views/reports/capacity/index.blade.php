@extends('layouts.app')

@section('title', 'Reports')

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Reports</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url('/dashboard') }}">Dashboard</a>
            </li>
            <li>
                Reports
            </li>
            <li class="active">
                <strong>Capacity Report</strong>
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
                    <h5>Capacity Report</h5>
                    @can('create', App\Repositories\Client\Client::class)
                        <div class="ibox-tools">
                            {{--<a href="{{ action('UserController@create') }}" class="btn btn-primary btn-xs">Create new user</a>--}}
                        </div>
                    @endcan
                </div>
                <div class="ibox-content">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Level</th>
                            <th>Authorised</th>
                            <th>Customers</th>
                            <th>Customer Capacity</th>
                            <th>Total Capacity</th>
                            <th>Customer types</th>
                            <th>Systems</th>
                            <th>Type of tasks</th>
                        </tr>
                        </thead>
                        <tbody>

                        @php($totalCapacity = 0)

                        @foreach($users as $user)
                            <?php
                                $customersCount = $user->clients(false)->count();
                            ?>
                            <tr>
                                <td>
                                    @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                                        <a href="{{ action('UserController@show', $user) }}">{{ $user->name }}</a>
                                    @else
                                        {{ $user->name }}
                                    @endif
                                    @if ($user->hasFlags())
                                        @include('flag-user.flagged', ['color' => $user->flagColor()])
                                    @endif
                                </td>
                                <td>Level: {{$user->level}}</td>
                                <td>{{$user->authorized ? 'Yes' : 'No'}}</td>
                                <td>{{$customersCount}}</td>
                                @if($user->level == 0)
                                    <td>0</td>
                                @elseif($user->level == 1)
                                    <td>5</td>
                                @elseif($user->level == 2)
                                    <td>10</td>
                                @else
                                    <td>{{round($user->weekly_capacity / $clientAverageTime)}}</td>
                                @endif
                                @if ($user->hasFlags())
                                    @if ($active = $user->lastFlag())
                                        @if($active->isEndless())
                                            <td>{{ 0 - $customersCount }}</td>
                                            <?php
                                                $totalCapacity -= $customersCount;
                                            ?>
                                        @else
                                            <td>0</td>
                                        @endif
                                    @endif
                                @elseif($user->level == 0)
                                    <td>0</td>
                                    <?php
                                        $totalCapacity += (0 - $customersCount);
                                    ?>
                                @elseif($user->level == 1)
                                    <td>{{5 - $customersCount}}</td>
                                    <?php
                                        $totalCapacity += (5 - $customersCount);
                                    ?>
                                @elseif($user->level == 2)
                                    @if($customersCount < 10)
                                    <td>{{$user->customer_capacity - $customersCount}}</td>
                                    <?php
                                        $totalCapacity += ($user->customer_capacity - $customersCount);
                                    ?>
                                    @else
                                        <td>{{10 - $customersCount}}</td>
                                        <?php
                                            $totalCapacity += (10 - $customersCount);
                                        ?>
                                    @endif
                                @else
                                    <td>{{round($user->weekly_capacity / $clientAverageTime) - $customersCount}}</td>
                                    <?php
                                        $totalCapacity += (max(round($user->weekly_capacity / $clientAverageTime) - $customersCount,0));
                                    ?>
                                @endif

                                <td class="align-middle">
                                    @foreach($user->customerTypes as $customerType)
                                        {{$customerType->name}}<br/>
                                    @endforeach
                                </td>
                                <td class="align-middle">
                                    @foreach($user->systems as $system)
                                        {{$system->name}}<br/>
                                    @endforeach
                                </td>
                                <td class="align-middle">
                                    @foreach($user->taskTypes as $taskType)
                                        {{$taskType->name}}<br/>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                            <b>Total Capacity : {{$totalCapacity}}</b>
                            <hr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
