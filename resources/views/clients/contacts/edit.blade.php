@extends('layouts.app')

@section('title', 'Edit contact')

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <h2>Edit client contact</h2>
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
            <li>
                <a href="{{ route('client.contacts.index', $client) }}">Contacts</a>
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
                    <h5>Edit contact for  <i>{{ $client->name }}</i></h5>
                </div>
                <div class="ibox-content">
                    <form id="form" class="form-horizontal" method="POST" action="{{ route('contacts.update', $contact) }}">
                        {{ csrf_field() }}

                        <input type="hidden" name="client_id" value="{{ $client->id }}" />

                        {{-- Name --}}
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="name">Name</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="name" id="name" value="{{ old('name', $contact->name) }}" required autofocus />

                                @if ($errors->has('name'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div class="form-group{{ $errors->has('notes') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="notes">Notes</label>

                            <div class="col-sm-10">
                                <textarea type="text" class="form-control" name="notes" id="notes">{{ old('notes', $contact->notes) }}</textarea>

                                @if ($errors->has('notes'))
                                    <span class="help-block m-b-none"><strong>{{ $errors->first('notes') }}</strong></span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group m-b-none">
                            <div class="col-sm-4 col-sm-offset-2">
                                <a class="btn btn-white" href="{{ url()->previous()}}">Cancel</a>
                                <button class="btn btn-primary" type="submit">Save</button>
                            </div>
                        </div>
                    </form>

                    <div class="hr-line-dashed"></div>

                    <div class="form-horizontal">
                        {{-- Phones --}}
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Phone</label>
                            <div class="col-sm-4">
                                @foreach($contact->phones as $phone)
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="+{{ $phone->number }}" readonly />

                                        {{--<span class="input-group-btn">--}}
                                            {{--<a href="{{ route('contacts.phone.edit', [$contact, $phone]) }}" class="btn btn-warning">Edit</a>--}}
                                        {{--</span>--}}

                                        <form id="form" class="input-group-btn" method="POST" action="{{ route('client.contact.phone.delete', [$client, $contact, $phone]) }}">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>

                                        @if(!$phone->isPrimary())
                                            <form id="form" class="input-group-btn" method="POST" action="{{ route('contacts.phone.primary', [$client, $contact, $phone]) }}">
                                                {{ csrf_field() }}
                                                <button type="submit" class="btn btn-default">Make primary</button>
                                            </form>
                                        @endif
                                    </div>
                                @endforeach
                                <a href="{{ route('client.contact.phone.create', [$client, $contact]) }}" class="btn btn-primary">+ Add new phone number</a>
                            </div>
                        </div>

                        {{-- Emails --}}
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Email</label>
                            <div class="col-sm-4">
                                @foreach($contact->emails as $email)
                                    <div class="input-group">
                                        <input type="email" class="form-control" value="{{ $email->address }}" readonly />

                                        {{--<span class="input-group-btn">--}}
                                            {{--<a href="{{ route('contacts.email.edit', [$contact, $email]) }}" class="btn btn-warning">Edit</a>--}}
                                        {{--</span>--}}

                                        <form id="form" class="input-group-btn" method="POST" action="{{ route('client.contact.email.delete', [$client, $contact, $email]) }}">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>

                                        @if(!$email->isPrimary())
                                            <form id="form" class="input-group-btn" method="POST" action="{{ route('client.contact.email.primary', [$client, $contact, $email]) }}">
                                                {{ csrf_field() }}
                                                <button type="submit" class="btn btn-default">Make primary</button>
                                            </form>
                                        @endif
                                    </div>
                                @endforeach
                                <a href="{{ route('client.contact.email.create', [$client, $contact]) }}" class="btn btn-primary">+ Add new email address</a>
                            </div>
                        </div>
                    </div>




                </div>
            </div>
        </div>
    </div>
</div>
@endsection
