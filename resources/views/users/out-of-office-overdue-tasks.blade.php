@extends('layouts.app')

@section('title', 'Out of office overdue tasks')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>Tasks that will become overdue in out of office period <b>{{ $from }} - {{ $to }}</b></h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li class="active">
                    <strong>Tasks that will become overdue</strong>
                </li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="wrapper wrapper-content animated fadeInUp" style="padding-bottom: 0;">
                <div class="alert alert-warning m-b-none">
                    <strong>Merk:</strong> Dette er en liste med oppgaver som antatt vil gå over fristen i perioden du planlegger å være borte. Det finnes ingen garanti for at ikke flere oppgaver vil dukke opp i listen. Det er derfor lurt at du ser over kundeporteføljen for å danne deg et bedre bilde og vurdere om noen av oppgavene må gjøres før du blir borte eller underveis. Dette gjelder spesielt om du har planer om å være borte i en lengre periode.
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="wrapper wrapper-content">

                {{-- Tasks --}}
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Tasks</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="task-list">
                            <table class="table table-hover">
                                <thead>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Client</th>
                                    <th class="text-right">Deadline</th>
                                </thead>
                                <tbody>
                                @foreach ($tasks as $task)
                                    <tr>
                                        <td>#{{$task->id}}</td>
                                        <td class="project-title">
                                            <a href="{{ action('TaskController@show', $task) }}">{{ $task->title }}</a>
                                        </td>
                                        <td>{{ $task->client->name }}</td>
                                        <td class="text-right">
                                            <span class="label label-{{ $task->deadlineClass() }}" style="{{ $task->isOverdue() && $task->overdueReason ? 'background-color:' . $task->overdueReason->overdueReason->hex : '' }}">@date($task->deadline)</span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <form class="form-horizontal" role="form" method="POST" action="{{ action('UserController@storeOutOfOffice', $user) }}">
                            {{ csrf_field() }}
                            <input type="hidden" name="reason" value="{{ $reason }}" />
                            <input type="hidden" name="from" value="{{ $from }}" />
                            <input type="hidden" name="to" value="{{ $to }}" />
                            <div class="form-group m-b-none">
                                <div class="col-sm-4">
                                    <button class="btn btn-primary" type="submit">I agree</button>
                                </div>
                            </div>
                        </form>
                    </div>


                </div>
            </div>
        </div>
    </div>
@endsection
