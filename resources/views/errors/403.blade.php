@extends('layouts.error')

@section('title', 'TMS - Something went wrong')

@section('content')
    <h1>403</h1>
    <h3 class="font-bold">Forbidden</h3>

    <div class="error-desc">
            You are not authorized to access this page.
            <br/><br/>
            Click the button below to navigate back.
            <br/>
            <a href="{{ env('APP_URL') }}" class="btn btn-primary m-t">Return back</a>
    </div>
@endsection