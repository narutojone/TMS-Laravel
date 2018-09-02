@extends('layouts.error')

@section('title', 'TMS - Something went wrong')

@section('content')
    <h1>401</h1>
    <h3 class="font-bold">Unauthorized</h3>

    <div class="error-desc">
            Missing or incorrect authentication credentials. Your session might have timed out.
            <br/><br/>
            Click the button below to navigate back.
            <br/>
            <a href="{{ env('APP_URL') }}" class="btn btn-primary m-t">Return back</a>
    </div>
@endsection