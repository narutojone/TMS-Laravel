@extends('layouts.auth')

@section('title', 'Register')

@section('content')
    <p>Register with your invitation!</p>

    <form class="m-t" role="form" method="POST" action="{{ action('InvitationController@submit', $invitation) }}">
        {{ csrf_field() }}

        {{-- Name --}}
        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
            <input id="name" type="text" class="form-control" name="name" placeholder="Name" value="{{ $invitation->name }}" required autofocus>

            @if ($errors->has('name'))
                <span class="help-block">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
            @endif
        </div>

        {{-- E-Mail Address --}}
        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <input id="email" type="email" class="form-control" name="email" placeholder="E-Mail Address" value="{{ $invitation->email }}" required>

            @if ($errors->has('email'))
                <span class="help-block">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif
        </div>

        {{-- Password --}}
        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
            <input id="password" type="password" class="form-control" name="password" placeholder="Password" required>

            @if ($errors->has('password'))
                <span class="help-block">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif
        </div>

        {{-- Confirm Password --}}
        <div class="form-group">
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password" required>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn btn-primary block full-width m-b">Register</button>
    </form>
@endsection
