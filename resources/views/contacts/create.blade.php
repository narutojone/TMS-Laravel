@extends('layouts.app')

@section('title', 'Create contact')

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Create contact</h2>
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
                <strong>Create</strong>
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
                    <h5>Create new contact</h5>
                </div>
                <div class="ibox-content">
                    <form id="form" class="form-horizontal" role="form" method="POST" action="{{ route('contacts.store', $client) }}">
                        {{ csrf_field() }}

                        {{-- Client --}}
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="name">Client</label>

                            <div class="col-sm-10">
                                <input type="hidden" name="client_id" id="client_id" value="{{ $client->id }}" />
                                <p class="form-control-static">{{ $client->name }}</p>
                            </div>
                        </div>

                        {{-- Name --}}
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="name">Name</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}" autofocus required/>

                                @if ($errors->has('name'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Email address --}}
                        <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="name">Email address</label>

                            <div class="col-sm-4">
                                <div class="m-b-sm">
                                    <input type="email" class="form-control" name="address" id="address" value="{{ old('address') }}" required />
                                </div>

                                @if ($errors->has('address'))
                                    <span class="help-block m-b-none"><strong>{{ $errors->first('address') }}</strong></span>
                                @endif
                            </div>
                        </div>

                        {{-- Phone numner --}}
                        <div class="form-group{{ $errors->has('number') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="name">Number</label>

                            <div class="col-sm-4">
                                <div class="input-group m-b-sm">
                                    <span class="input-group-addon">+</span>
                                    <input type="text" class="form-control" name="number" id="number" value="{{ old('number') }}" />
                                </div>

                                @if ($errors->has('number'))
                                    <span class="help-block m-b-none"><strong>{{ $errors->first('number') }}</strong></span>
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

                        {{-- Notes --}}
                        <div class="form-group{{ $errors->has('notes') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="notes">Notes</label>

                            <div class="col-sm-10">
                                <textarea class="form-control" name="notes" id="notes">{{ old('notes') }}</textarea>

                                @if ($errors->has('notes'))
                                    <span class="help-block m-b-none"><strong>{{ $errors->first('notes') }}</strong></span>
                                @endif
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group m-b-none">
                            <div class="col-sm-4 col-sm-offset-2">
                                <a class="btn btn-white" href="{{ route('contacts.index') }}">Cancel</a>
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

