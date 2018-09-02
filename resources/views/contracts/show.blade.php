@extends('layouts.app')

@section('title', "View contract details")

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>View contract details</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                    <a href="{{ route('clients.index') }}">Clients</a>
                </li>
                <li>
                    <a href="{{ route('client.show', $contract->client) }}">{{ $contract->client->name }}</a>
                </li>
                <li class="active">
                    <strong>Contract</strong>
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
                            <div class="col-md-2">Contract id</div>
                            <div class="col-md-10">#{{ $contract->id }}</div>
                        </div>

                        <div class="hr-line-dashed m-t-sm m-b-sm"></div>

                        <div class="row">
                            <div class="col-md-2">Client name</div>
                            <div class="col-md-10"><a href="{{ route('client.show', $contract->client) }}">{{ $contract->client->name }}</a></div>
                        </div>

                        <div class="hr-line-dashed m-t-sm m-b-sm"></div>

                        <div class="row">
                            <div class="col-md-2">Created at</div>
                            <div class="col-md-10">{{ $contract->created_at }}</div>
                        </div>

                        <div class="hr-line-dashed m-t-sm m-b-sm"></div>

                        <div class="row">
                            <div class="col-md-2">Created by</div>
                            <div class="col-md-10">{{ $contract->author->name }} (#{{ $contract->author->id }})</div>
                        </div>

                        <div class="hr-line-dashed m-t-sm m-b-sm"></div>

                        <div class="row">
                            <div class="col-md-2">Start date</div>
                            <div class="col-md-10">{{ $contract->start_date }}</div>
                        </div>

                        <div class="hr-line-dashed m-t-sm m-b-sm"></div>

                        <div class="row">
                            <div class="col-md-2">End date</div>
                            <div class="col-md-10">{{ $contract->end_date ? $contract->end_date : '-' }}</div>
                        </div>

                        <div class="hr-line-dashed m-t-sm m-b-sm"></div>

                        @if($contract->one_time)
                            <div class="row">
                                <div class="col-md-2">One time contract</div>
                                <div class="col-md-10">{{ $contract->one_time ? 'Yes' : 'No' }}</div>
                            </div>
                        @else
                            <div class="row">
                                <div class="col-md-2">Under 50 bills</div>
                                <div class="col-md-10">{{ $contract->under_50_bills ? 'Yes' : 'No' }}</div>
                            </div>

                            <div class="hr-line-dashed m-t-sm m-b-sm"></div>

                            <div class="row">
                                <div class="col-md-2">Shareholder Registry</div>
                                <div class="col-md-10">{{ $contract->shareholder_registry ? 'Yes' : 'No' }}</div>
                            </div>

                            <div class="hr-line-dashed m-t-sm m-b-sm"></div>

                            <div class="row">
                                <div class="col-md-2">Bank Reconciliation</div>
                                <div class="col-md-10">{{ $contract->bank_reconciliation ? $contract->bank_reconciliation_date : 'No' }}</div>
                            </div>

                            <div class="hr-line-dashed m-t-sm m-b-sm"></div>

                            <div class="row">
                                <div class="col-md-2">Bookkeeping</div>
                                <div class="col-md-10">{{ $contract->bookkeeping ? $contract->bookkeeping_date : 'No' }}</div>
                            </div>

                            <div class="hr-line-dashed m-t-sm m-b-sm"></div>

                            <div class="row">
                                <div class="col-md-2">MVA</div>
                                <div class="col-md-10">{{ $contract->mva ? \App\Repositories\Contract\Contract::$mvaTypes[$contract->mva_type] : 'No' }}</div>
                            </div>

                            <div class="hr-line-dashed m-t-sm m-b-sm"></div>

                            <div class="row">
                                <div class="col-md-2">Financial statements</div>
                                <div class="col-md-10">{{ $contract->financial_statements ? $contract->financial_statements_year : 'No' }}</div>
                            </div>

                            <div class="hr-line-dashed m-t-sm m-b-sm"></div>

                            <div class="row">
                                <div class="col-md-2">Salary check</div>
                                <div class="col-md-10">{{ $contract->salary_check ? 'Yes' : 'No' }}</div>
                            </div>

                            <div class="hr-line-dashed m-t-sm m-b-sm"></div>

                            <div class="row">
                                <div class="col-md-2">Salary</div>
                                <div class="col-md-10">
                                    @if($contract->salary)
                                        Days:
                                        @foreach($contract->salaryDays as $salaryDay)
                                            @if(array_key_exists($salaryDay->day, \App\Repositories\ContractSalaryDay\ContractSalaryDay::$specialSalaryDays))
                                                {{ \App\Repositories\ContractSalaryDay\ContractSalaryDay::$specialSalaryDays[$salaryDay->day] }},
                                            @else
                                                {{ $salaryDay->day }},
                                            @endif
                                        @endforeach
                                    @else
                                        No
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($contract->active)
                            @can('terminate', $contract)
                                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <form method="post" action="{{ route('contracts.terminate', $contract) }}">
                                            {{ csrf_field() }}
                                            <button type="submit" class="btn btn-danger btn-outline pull-right"><i class="fa fa-unlink"></i> Terminate</button>
                                        </form>
                                    </div>
                                </div>
                            @endcan
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
