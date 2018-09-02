@extends('layouts.auth')

@section('title', '')

@section('content')
    <p>Reset your password</p>

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <form class="m-t" role="form" method="POST" action="{{ url('/password/reset') }}">
        {{ csrf_field() }}

        {{-- Password Reset Token --}}
        <input type="hidden" name="token" value="{{ $token }}">

        {{-- E-Mail Address --}}
        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <input type="email" class="form-control" name="email" placeholder="E-Mail Address" value="{{ $email or old('email') }}" required autofocus>

            @if ($errors->has('email'))
                <span class="help-block">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif
        </div>

        {{-- Password --}}
        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
            <input type="password" class="form-control" name="password" placeholder="Password" required>

            @if ($errors->has('password'))
                <span class="help-block">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif
        </div>

        {{-- Confirm Password --}}
        <div class="form-group">
            <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password" required>

            @if ($errors->has('password_confirmation'))
                <span class="help-block">
                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                </span>
            @endif
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn btn-primary block full-width m-b">Reset Password</button>
    </form>
@endsection
