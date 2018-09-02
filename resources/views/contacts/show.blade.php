@extends('layouts.app')

@section('title', "Contact: {$contact->name}")

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>{{ $contact->name }}</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                    @can ('index', \App\Repositories\Contact\Contact::class)
                        <a href="{{ route('contacts.index') }}">Contacts</a>
                    @else
                        Contacts
                    @endif
                </li>
                <li class="active">
                    <strong>{{ $contact->name }}</strong>
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
                            <div class="col-md-2">Name</div>
                            <div class="col-md-10"><b>{{ $contact->name }}</b></div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="row">
                            <div class="col-md-2">Note</div>
                            <div class="col-md-10"><i>{{ $contact->notes ? $contact->notes : '-' }}</i></div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="row">
                            <div class="col-md-2">Phone</div>
                            <div class="col-md-10">
                                <ul class="unstyled no-padding">
                                @forelse($contact->phones as $phone)
                                    <li class="m-b-sm">
                                        {{ $phone->number }}
                                        @if($phone->primary)
                                            <span class="label label-primary m-l-md">Primary</span>
                                        @endif
                                    </li>
                                @empty
                                    <li>No phone numbers added</li>
                                @endforelse
                                </ul>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="row">
                            <div class="col-md-2">Email</div>
                            <div class="col-md-10">
                                <ul class="unstyled no-padding">
                                    @forelse($contact->emails as $email)
                                        <li class="m-b-sm">
                                            {{ $email->address }}
                                            @if($email->primary)
                                                <span class="label label-primary m-l-md">Primary</span>
                                            @endif
                                        </li>
                                    @empty
                                        <li>No email addresses added</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="row">
                            <div class="col-md-2">Clients</div>
                            <div class="col-md-10">
                                <ul class="unstyled no-padding">
                                    @forelse($contact->clients as $client)
                                        <li class="m-b-sm">
                                            <a href="{{ route('client.show', $client) }}">{{ $client->name }}</a>
                                        </li>
                                    @empty
                                        <li>No email addresses added</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
