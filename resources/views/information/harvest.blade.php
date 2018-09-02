@extends('layouts.app')
@section('title', 'Timeføring (Harvest)')
@section('head')
    <style>
        .navbar-default{display:none;}
        .navbar{display:none;}
        #page-wrapper{margin:0px;}
    </style>
@append
@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2></h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li class="active">
                    <strong>Timeføring (Harvest)</strong>
                </li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="wrapper wrapper-content">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Informasjon angående Harvest</h5>
                    </div>
                    <div class="ibox-content">
                        <h3 class="m-b-md">Tiden du fører på fakturerbar tid havner direkte på kundens faktura, så når du skal føre timer er det et par ting som er greit å huske:</h3>

                        <ol style="font-weight: bold;">
                            <li>Før tid på en slik måte at fakturaen blir seende ut slik du hadde ønsket at den skulle se ut, om du selv var kunde. Føringer i Harvest skal henvise ( ha sporbarhet) til utført oppgave i TMS og kundens økonomiprogram. F.eks er det satt opp bankavstemming i harvest skal oppgaven være utført i TMS og i kundens økonomiprogram.</li>
                            <li>Om du er usikker på hvordan du skal føre tid kan du lese mer om dette under "FAQ". Om du er derimot er usikker på hvordan Harvest fungerer eller hvordan du kan benytte Harvest inne i Zendesk kan du leser mer om dette i "FAQ" under Harvest.</li>
                            <li>For flere detaljer - legg til enkle kommentarer som er enkle å forstå for kunden. Om du eksempelvis fører tid under bankavstemming skriver du i teksten for hvilke måneder det gjelder - "Jan. - Feb.".</li>
                            <li>Ikke legg inn tekst som kunden ikke har grunnlag for å forstå, eksempelvis "Lagret i TMS", siden kunder ikke vet hva TMS er, så skriv heller "Lagret i kundemappe".</li>
                            <li>Vi selger minutter til kunder, så ikke før hele og halve timer, men bruk stoppeklokkefunksjonen.</li>
                        </ol>

                        <div class="hr-line-dashed"></div>

                        <a class='btn btn-primary' href="https://synega.onelogin.com/launch/618071" target="_blank">Jeg har lest og forstått informasjonen</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
