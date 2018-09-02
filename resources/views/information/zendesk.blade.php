@extends('layouts.app')
@section('title', 'E-Mail (Zendesk)')
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
                    <strong>E-Mail (Zendesk)</strong>
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
                        <h5>Informasjon angående Zendesk</h5>
                    </div>
                    <div class="ibox-content">
                        <h3 class="m-b-md">Husk at kommunikasjon ut mot kunde alltid skal være av en profesjonell karakter. Du jobber under Accounting Groups merkevare og dette skal reflekteres i kommunikasjonen din:</h3>

                        <ol style="font-weight: bold;">
                            <li>Vær forsiktig med å gi bindene svar ut til kunde da dette igjen binder både deg og oss om det skulle bli klagesaker/erstatningskrav senere.</li>
                            <li>Interne forhold skal tas internt og ikke ut mot kunde. Et eksempel er om du tar over en kunde fra en tidligere konsulent der noe er feil og/eller må ryddes opp.</li>
                            <li>Om du synes noe av AGs oppsett ikke er optimalt skal dette også tas opp internt og ikke med kunden. Send forslag til forbedringer via "? Oppdatere" knappen i TMS.</li>
                            <li>Bruk hele setninger, riktig tegnsetting og unngå smileys, slang, banneord, og så videre.</li>
                            <li>Hold kunden løpende oppdatert om det brukes veldig mange flere timer en måned enn det som har vært normalt, slik at vi slipper unødvendig klagesaker.</li>
                        </ol>

                        <p>Å snakke ned selskapet eller andre konsulenter mot kunder tjener ingen hensikt og skader merkevaren som så mange jobber hver dag for å bygge opp, samtidig som mange kunder oppfatter det som uprofesjonelt av deg. Om det dukker opp problemer eller om det er ting du ønsker å ta opp er vi i administrasjonen alltid klare til høre på deg.</p>

                        <div class="hr-line-dashed"></div>

                        <a class='btn btn-primary' href="https://synega.onelogin.com/launch/617623" target="_blank">Jeg har lest og forstått informasjonen</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
