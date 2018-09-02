@extends('layouts.app')

@section('title', 'Edit contact phone number')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2>Edit contact phone number</h2>
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
                        <h5>Edit contact phone number</h5>
                    </div>
                    <div class="ibox-content">
                        <form id="form" class="form-horizontal" method="POST" action="{{ route('contacts.phone.update', [$contact, $phone]) }}">
                            {{ csrf_field() }}

                            {{-- Phone number --}}
                            <div class="form-group{{ $errors->has('number') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="number">Number</label>

                                <div class="col-sm-4 input-group">
                                    <span class="input-group-addon">+</span>
                                    <input type="text" class="form-control" name="number" id="number" value="{{ old('number', $phone->number) }}" required autofocus />

                                    @if ($errors->has('number'))
                                        <span class="help-block m-b-none"><strong>{{ $errors->first('number') }}</strong></span>
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
