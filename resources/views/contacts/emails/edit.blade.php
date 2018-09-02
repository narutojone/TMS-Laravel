@extends('layouts.app')

@section('title', 'Edit contact email address')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2>Edit contact email address</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                    @can('index', \App\Repositories\Contact\Contact::class)
                        <a href="{{ route('contacts.index') }}">Contacts</a>
                    @else
                        Contacts
                    @endcan
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
                        <h5>Edit contact email address</h5>
                    </div>
                    <div class="ibox-content">
                        <form id="form" class="form-horizontal" method="POST" action="{{ route('contacts.email.update', [$contact, $email]) }}">
                            {{ csrf_field() }}

                            {{-- Email address --}}
                            <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="number">Email address</label>

                                <div class="col-sm-4">
                                    <input type="email" class="form-control" name="address" id="address" value="{{ old('address', $email->address) }}" required autofocus />

                                    @if ($errors->has('address'))
                                        <span class="help-block m-b-none"><strong>{{ $errors->first('address') }}</strong></span>
                                    @endif
                                </div>
                            </div>

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
