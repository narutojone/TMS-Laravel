@extends('layouts.app')

@section('title', 'Contacts')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2></h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li class="active">
                    <strong>Contacts</strong>
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
                        <h5>Contacts list</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="project-list">
                            <table class="table table-hover">
                                @if($contacts->count())
                                    <thead>
                                        <th>Name</th>
                                        <th>Main phone</th>
                                        <th>Main email</th>
                                        <th class="project-actions">Actions</th>
                                    </thead>
                                @endif
                                <tbody>
                                    @forelse($contacts as $contact)
                                        @php ($phonesCount = count($contact->phones))
                                        @php ($emailsCount = count($contact->emails))
                                        <tr>
                                            <td class="project-title">{{ $contact->name }}</td>
                                            <td class="project-title">
                                                @if($phonesCount)
                                                    +{{ $contact->phones->first()->number }}
                                                    @if($phonesCount > 1)
                                                        <span class="label label-primary m-l-md">+{{ $phonesCount - 1 }} more</span>
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="project-title">
                                                @if($emailsCount)
                                                    {{ $contact->emails->first()->address }}
                                                    @if($emailsCount > 1)
                                                        <span class="label label-primary m-l-md">+{{ $emailsCount - 1 }} more</span>
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            <td class="project-actions">
                                                @can('view', $contact)
                                                    <a href="{{ route('contacts.show', $contact) }}" class="btn btn-white btn-sm">
                                                        <i class="fa fa-folder"></i> View
                                                    </a>
                                                @endcan
                                                {{--@can('edit', $contact)--}}
                                                    {{--<a href="{{ route('contacts.edit', $contact) }}" class="btn btn-white btn-sm">--}}
                                                        {{--<i class="fa fa-pencil"></i> Edit--}}
                                                    {{--</a>--}}
                                                {{--@endcan--}}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td>No contacts...</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
