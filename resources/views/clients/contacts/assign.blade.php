@extends('layouts.app')

@section('title', 'Assign contact')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2>Add contact</h2>
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
                    <strong>Add</strong>
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
                        <h5>Assign contact for client {{ $client->name }}</h5>
                    </div>
                    <div class="ibox-content">
                        <form id="form" class="form-horizontal" role="form" method="POST" action="{{ route('client.contacts.link.store', $client) }}">
                            {{ csrf_field() }}

                            {{-- Contact person --}}
                            <div class="form-group{{ $errors->has('client_id') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="contact_id">Contact</label>

                                <div class="col-sm-10">
                                    <select class="form-control chosen-select" name="contact_id" id="contact_id" autofocus>
                                        @foreach ($contacts as $contact)
                                            <option value="{{ $contact->id }}"{{ (old('contact_id') == $contact->id) ? ' selected' : '' }}>{{ $contact->name }}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('contact_id'))
                                        <span class="help-block m-b-none"><strong>{{ $errors->first('contact_id') }}</strong></span>
                                    @endif
                                </div>
                            </div>

                            {{-- Primary --}}
                            <div class="form-group{{ $errors->has('primary') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="primary">Primary</label>

                                <div class="col-sm-10">
                                    <input type="hidden" name="primary" value="0" />
                                    <input type="checkbox" value="1" class="js-switch" id="primary" name="primary" {{ old('primary') ? 'checked' : ''}} />

                                    @if ($errors->has('primary'))
                                        <span class="help-block m-b-none"><strong>{{ $errors->first('primary') }}</strong></span>
                                    @endif
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>

                            <div class="form-group m-b-none">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <a class="btn btn-white" href="{{ route('client.contacts.index', $client) }}">Cancel</a>
                                    <button class="btn btn-primary" type="submit">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

