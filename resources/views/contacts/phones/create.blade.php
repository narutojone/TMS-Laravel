@extends('layouts.app')

@section('title', 'Create contact phone')

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Create contact phone</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url('/dashboard') }}">Dashboard</a>
            </li>
            <li>
                <a href="{{ route('contacts.index') }}">Contacts</a>
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
                    <h5>Create new contact phone</h5>
                </div>
                <div class="ibox-content">
                    <form id="form" class="form-horizontal" role="form" method="POST" action="{{ route('client.contact.phone.store', [$client, $contact]) }}">
                        {{ csrf_field() }}

                        {{-- Contact person --}}
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="name">Contact</label>

                            <div class="col-sm-10">
                                <input type="hidden" name="contact_id" value="" />
                                <p class="form-control-static">{{ $contact->name }}</p>
                            </div>
                        </div>

                        {{-- Phone numner --}}
                        <div class="form-group{{ $errors->has('number') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="name">Number</label>

                            <div class="col-sm-4">
                                <div class="input-group m-b-sm">
                                    <span class="input-group-addon">+</span>
                                    <input type="text" class="form-control" name="number" id="number" value="{{ old('number') }}" autofocus />
                                </div>

                                @if ($errors->has('number'))
                                    <span class="help-block m-b-none"><strong>{{ $errors->first('number') }}</strong></span>
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

