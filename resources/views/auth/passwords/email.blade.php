@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
    <p>Reset your password</p>

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <form class="m-t" role="form" method="POST" action="{{ url('/password/email') }}">
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

        {{-- Submit --}}
        <button type="submit" class="btn btn-primary block full-width m-b">Send Password Reset Link</button>

        <a href="{{ url('/login') }}"><small>Login</small></a>
    </form>
@endsection
