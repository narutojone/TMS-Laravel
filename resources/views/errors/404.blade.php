@extends('layouts.error')

@section('title', 'TMS - Something went wrong')

@section('content')
    <h1>404</h1>
    <h3 class="font-bold">Not Found</h3>

    <div class="error-desc">
            The page you are looking for can't be found. Maybe you tried to access an old page?
            <br/><br/>
            Click the button below to navigate back.
            <br/>
            <a href="{{ env('APP_URL') }}" class="btn btn-primary m-t">Return back</a>
    </div>
@endsection