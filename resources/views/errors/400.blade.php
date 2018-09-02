@extends('layouts.error')

@section('title', 'TMS - Something went wrong')

@section('content')
    <h1>400</h1>
    <h3 class="font-bold">Bad Request</h3>

    <div class="error-desc">
            The request was invalid or cannot be otherwise served. An accompanying error message will explain further. Requests without authentication are considered invalid and will yield this response.
            <br/><br/>
            Click the button below to navigate back.
            <br/>
            <a href="{{ env('APP_URL') }}" class="btn btn-primary m-t">Return back</a>
    </div>
@endsection