@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <p>Login and get started!</p>

    <form class="m-t" role="form" method="POST" action="{{ url('/login') }}">
        {{ csrf_field() }}

        {{-- E-Mail Address --}}
        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <input type="email" class="form-control" name="email" placeholder="E-Mail Address" value="{{ old('email') }}" required autofocus>

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

        {{-- Remember me --}}
        <div class="form-group">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="remember"> Remember Me
                </label>
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn btn-primary block full-width m-b">Login</button>

        <a href="https://synega.onelogin.com/launch/654306"><small>Forgot Your Password?</small></a>
    </form>
@endsection
