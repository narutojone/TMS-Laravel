@extends('layouts.app')

@section('title', "Review contract details")

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>Review contract details</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                    <a href="{{ route('clients.index') }}">Clients</a>
                </li>
                <li>
                    <a href="{{ route('client.show', $client) }}">{{ $client->name }}</a>
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
                            <div class="col-md-2">Kunde</div>
                            <div class="col-md-10"><a href="{{ route('client.show', $client) }}">{{ $client->name }}</a></div>
                        </div>

                        <div class="hr-line-dashed m-t-sm m-b-sm"></div>

                        <div class="row">
                            <div class="col-md-2">Start dato</div>
                            <div class="col-md-10">{{ $contract['start_date'] }}</div>
                        </div>

                        <div class="hr-line-dashed m-t-sm m-b-sm"></div>

                        @if($contract['one_time'])
                            <div class="row">
                                <div class="col-md-2">Enkelttimer</div>
                                <div class="col-md-10">{{ $contract['one_time'] ? 'Ja' : 'Nei' }}</div>
                            </div>
                        @else
                            <div class="row">
                                <div class="col-md-2">Under 50 bilag</div>
                                <div class="col-md-10">{{ $contract['under_50_bills'] ? 'Ja' : 'Nei' }}</div>
                            </div>

                            <div class="hr-line-dashed m-t-sm m-b-sm"></div>

                            <div class="row">
                                <div class="col-md-2">Aksjonærregisteroppgave</div>
                                <div class="col-md-10">{{ $contract['shareholder_registry'] ? 'Ja' : 'Nei' }}</div>
                            </div>

                            <div class="hr-line-dashed m-t-sm m-b-sm"></div>

                            <div class="row">
                                <div class="col-md-2">Kontroll av regnskap</div>
                                <div class="col-md-10">{{ $contract['control_client'] ? 'Ja' : 'Nei' }}</div>
                            </div>

                            <div class="hr-line-dashed m-t-sm m-b-sm"></div>

                            <div class="row">
                                <div class="col-md-2">Avstemming</div>
                                <div class="col-md-10">{{ $contract['bank_reconciliation'] ? $contract['bank_reconciliation_date'] : 'Nei' }}</div>
                            </div>

                            <div class="hr-line-dashed m-t-sm m-b-sm"></div>

                            <div class="row">
                                <div class="col-md-2">Bokføring</div>
                                <div class="col-md-10">{{ $contract['bookkeeping'] ? $contract['bookkeeping_date'] : 'Nei' }}</div>
                            </div>

                            <div class="hr-line-dashed m-t-sm m-b-sm"></div>

                            <div class="row">
                                <div class="col-md-2">MVA</div>
                                <div class="col-md-10">{{ $contract['mva'] ? \App\Repositories\Contract\Contract::$mvaTypes[$contract['mva_type']] : 'Nei' }}</div>
                            </div>

                            <div class="hr-line-dashed m-t-sm m-b-sm"></div>

                            <div class="row">
                                <div class="col-md-2">Årsoppgjør</div>
                                <div class="col-md-10">{{ $contract['financial_statements'] ? $contract['financial_statements_year'] : 'Nei' }}</div>
                            </div>

                            <div class="hr-line-dashed m-t-sm m-b-sm"></div>

                            <div class="row">
                                <div class="col-md-2">Kontroll av lønn</div>
                                <div class="col-md-10">{{ $contract['salary_check'] ? 'Ja' : 'Nei' }}</div>
                            </div>

                            <div class="hr-line-dashed m-t-sm m-b-sm"></div>

                            <div class="row">
                                <div class="col-md-2">Lønn</div>
                                <div class="col-md-10">
                                    @if($contract['salary'])
                                        Days:
                                        @foreach($contract['salary_day'] as $salaryDay)
                                            @if(array_key_exists($salaryDay, \App\Repositories\ContractSalaryDay\ContractSalaryDay::$specialSalaryDays))
                                                {{ \App\Repositories\ContractSalaryDay\ContractSalaryDay::$specialSalaryDays[$salaryDay] }},
                                            @else
                                                {{ $salaryDay }},
                                            @endif
                                        @endforeach
                                    @else
                                        No
                                    @endif
                                </div>
                            </div>
                        @endif

                    </div>
                </div>

                <div class="ibox">
                    <div class="ibox-content">
                        @if(isset($tasks['newTasks']) && sizeof($tasks['newTasks']) > 0)
                            <h3>Oppgaver som vil bli opprettet</h3>
                            <table class="table table-hover">
                                <thead>
                                    <th></th>
                                    <th>Title</th>
                                    <th>Repeating</th>
                                    <th>Frequency</th>
                                    <th>Deadline</th>
                                </thead>
                                <tbody>
                                    @foreach($tasks['newTasks'] as $task)
                                        <tr>
                                            <td><input type="checkbox" checked disabled /></td>
                                            <td>{{ $templates[$task['template']]['title'] }}</td>
                                            <td>{{ $task['repeating'] ? 'Yes' : 'No' }}</td>
                                            <td>{{ $task['repeating'] ? $task['frequency'] : '-' }}</td>
                                            <td>@date($task['deadline'])</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                        @if(isset($tasks['tasksToBeDeleted']) && sizeof($tasks['tasksToBeDeleted']) > 0)
                            <h3>Oppgaver som vil bli slettet</h3>
                            <table class="table table-hover">
                                <thead>
                                    <th></th>
                                    <th>Title</th>
                                    <th>Repeating</th>
                                    <th>Frequency</th>
                                    <th>Deadline</th>
                                </thead>
                                <tbody>
                                    @foreach($tasks['tasksToBeDeleted'] as $task)
                                        <tr>
                                            <td><input type="checkbox" checked disabled name="name" /></td>
                                            <td>{{ $task->template->title }}</td>
                                            <td>{{ $task->repeating ? 'Yes' : 'No' }}</td>
                                            <td>{{ $task->repeating ? $task->frequency : '-' }}</td>
                                            <td>@date($task->deadline)</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                        @if(isset($tasks['tasksToBeUpdated']) && sizeof($tasks['tasksToBeUpdated']) > 0)
                            <h3>Oppgaver som vil bli oppdatert</h3>
                            <table class="table table-hover">
                                <thead>
                                    <th></th>
                                    <th>Title</th>
                                    <th>Repeating</th>
                                    <th>Frequency</th>
                                    <th>Deadline</th>
                                </thead>
                                <tbody>
                                    @foreach($tasks['tasksToBeUpdated'] as $task)
                                        <tr>
                                            <td><input type="checkbox" checked disabled /></td>
                                            <td>{{ $templates[$task['template']]['title'] }}</td>
                                            <td>{{ $task['repeating'] ? 'Yes' : 'No' }}</td>
                                            <td>{{ $task['repeating'] ? $task['frequency'] : '-' }}</td>
                                            <td>@date($task['deadline'])</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                        @if(isset($tasks['tasksToBeSkipped']) && sizeof($tasks['tasksToBeSkipped']) > 0)
                            <h3>Oppgaver som ikke vil bli endret</h3>
                            <table class="table table-hover">
                                <thead>
                                    <th></th>
                                    <th>Title</th>
                                    <th>Assignee</th>
                                    <th>Repeating</th>
                                    <th>Frequency</th>
                                </thead>
                                <tbody>
                                @foreach($tasks['tasksToBeSkipped'] as $task)
                                    <tr>
                                        <td><input type="checkbox" checked disabled name="name" /></td>
                                        <td>{{ $task->template->title }}</td>
                                        <td>{{ $task->user ? $task->user->name : '-' }}</td>
                                        <td>{{ $task->repeating ? 'Yes' : 'No' }}</td>
                                        <td>{{ $task->repeating ? $task->frequency : '-' }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif

                        <div class="hr-line-dashed"></div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <span class="help-block m-b-md">Denne handlingen kan ikke angres på. Oppgavene som er spesifisert over vil bli opprettet/endret/slettet slik det står. Om du mener at noe ikke stemmer, ta kontakt med IT-Support <strong>FØR</strong> du aktiverer den nye kontrakten.<br><br><strong>MERK: Kunden vil motta en e-post som spesifiserer at du har endret kontrakten og hvilke aktive tjenester kunden nå har hos oss.</strong></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <form id="form" role="form" method="POST" action="{{ route('contracts.store') }}">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="client_id" value="{{ $contract['client_id'] }}" />
                                    <input type="hidden" name="start_date" value="{{ $contract['start_date'] }}" />
                                    <input type="hidden" name="one_time" value="{{ $contract['one_time'] }}" />
                                    <input type="hidden" name="under_50_bills" value="{{ $contract['under_50_bills'] }}" />
                                    <input type="hidden" name="shareholder_registry" value="{{ $contract['shareholder_registry'] }}" />
                                    <input type="hidden" name="control_client" value="{{ $contract['control_client'] }}" />
                                    <input type="hidden" name="bank_reconciliation" value="{{ $contract['bank_reconciliation'] }}" />
                                    <input type="hidden" name="bank_reconciliation_date" value="{{ $contract['bank_reconciliation_date'] }}" />
                                    <input type="hidden" name="bank_reconciliation_frequency_custom" value="{{ $contract['bank_reconciliation_frequency_custom'] }}" />
                                    <input type="hidden" name="bank_reconciliation_frequency" value="{{ $contract['bank_reconciliation_frequency'] }}" />
                                    <input type="hidden" name="bookkeeping" value="{{ $contract['bookkeeping'] }}" />
                                    <input type="hidden" name="bookkeeping_date" value="{{ $contract['bookkeeping_date'] }}" />
                                    <input type="hidden" name="bookkeeping_frequency_custom" value="{{ $contract['bookkeeping_frequency_custom'] }}" />
                                    <input type="hidden" name="bookkeeping_frequency_1" value="{{ $contract['bookkeeping_frequency_1'] }}" />
                                    <input type="hidden" name="bookkeeping_frequency_2" value="{{ $contract['bookkeeping_frequency_2'] }}" />
                                    <input type="hidden" name="mva" value="{{ $contract['mva'] }}" />
                                    <input type="hidden" name="mva_type" value="{{ $contract['mva_type'] }}" />
                                    <input type="hidden" name="financial_statements" value="{{ $contract['financial_statements'] }}" />
                                    <input type="hidden" name="financial_statements_year" value="{{ $contract['financial_statements_year'] }}" />
                                    <input type="hidden" name="salary_check" value="{{ $contract['salary_check'] }}" />
                                    <input type="hidden" name="salary" value="{{ $contract['salary'] }}" />
                                    @foreach($contract['salary_day'] as $salaryDay)
                                        <input type="hidden" name="salary_day[]" value="{{ $salaryDay }}" />
                                    @endforeach
                                    <button type="submit" class="btn btn-primary pull-left"><i class="fa fa-check"></i> Save contract</button>
                                </form>
                                <a href="{{ route('client.show', $client) }}">
                                    <button class="btn btn-danger pull-left m-l"><i class="fa fa-times"></i> Cancel</button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
