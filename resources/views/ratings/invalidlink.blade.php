@extends('layouts.error')

@section('title', 'Accounting Group AS')

@section('content')
    <h1>404</h1>
    <h3 class="font-bold">Siden ble ikke funnet</h3>

    <div class="error-desc">
            Det kan se ut som du allerede har gitt oss en tilbakemelding.<br>
            Du kan alltid kontakte kundeservice direkte på <a href="mailto:{{ 'hjelp@acgr.no' }}">{{ 'hjelp@acgr.no' }}</a> om du skulle ha noen problemer.
    </div>
@endsection