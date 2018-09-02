@extends('layouts.app')

@section('title', 'Client contacts')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2></h2>
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
                    <strong>Client contacts</strong>
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
                        <h5>Contacts list for {{ $client->name }}</h5>

                        <div class="ibox-tools">
                            @can('update', $client)
                                <a href="{{ route('client.contacts.link', $client) }}" class="btn btn-primary btn-xs">Assign existing contact</a>
                            @endcan

                            @can('create', [\App\Repositories\Contact\Contact::class, $client])
                                <a href="{{ route('client.contacts.create', $client) }}" class="btn btn-primary btn-xs">+ Create new contact</a>
                            @endcan
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="project-list">
                            <table class="table table-hover">
                                @if($client->contacts->count())
                                    <thead>
                                    <th>Name</th>
                                    <th>Main phone</th>
                                    <th>Main email</th>
                                    <th class="project-actions">Actions</th>
                                    </thead>
                                @endif
                                <tbody>
                                @forelse($client->contacts as $contact)
                                    @php ($phonesCount = count($contact->phones))
                                    @php ($emailsCount = count($contact->emails))
                                    <tr>
                                        <td class="project-title">
                                            {{ $contact->name }}
                                            @if($contact->isPrimary())
                                                <span class="label label-primary m-l-md">primary</span>
                                            @endif
                                        </td>
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
                                        </td>
                                        <td class="project-actions">
                                            @can('edit', $contact)
                                                <a href="{{ route('client.contact.edit', [$client, $contact]) }}" class="btn btn-white btn-sm">
                                                    <i class="fa fa-pencil"></i> Edit
                                                </a>
                                            @endcan
                                            @can('update', $client)
                                                <form method="post" class="inline" action="{{ route('client.contacts.remove', [$client, $contact]) }}">
                                                    {{ csrf_field() }}
                                                    <button type="submit" class="btn btn-white btn-sm" ><i class="fa fa-close"></i> Unlink</button>
                                                </form>
                                            @endcan
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
