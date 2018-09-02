@extends('layouts.error')

@section('title', 'TMS - Something went wrong')

@section('content')
    <h1>500</h1>
    <h3 class="font-bold">Internal Server Error</h3>

    <div class="error-desc">
            The server encountered something unexpected that didn't allow it to complete the request. We apologize.
            <br/><br/>
            Click the button below to navigate back.
            <br/>
            <a href="{{ env('APP_URL') }}" class="btn btn-primary m-t">Return back</a>
    </div>

    @unless(empty($sentryID))
         <!-- Sentry JS SDK 2.1.+ required -->
         <script src="https://cdn.ravenjs.com/3.3.0/raven.min.js"></script>

         <script>
         Raven.showReportDialog({
             eventId: '{{ $sentryID }}',

             // use the public DSN (dont include your secret!)
             dsn: 'https://62a345c60299409384f2a647270d3029@sentry.io/216885'
         });
         </script>
    @endunless

@endsection

