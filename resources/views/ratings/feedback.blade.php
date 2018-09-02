@extends('layouts.plain')

@section('title', 'Accounting Group AS')

@section('content')
<h2 style="font-weight: bold;font-size: 32px;line-height: 42px;">Takk!</h2>
<p style="font-size: 16px;line-height: 23px;color: #616161;mso-line-height-rule: exactly;">Siden du ikke er helt tilfreds setter vi pris på en kort tilbakemelding på hvordan konsulenten kan forbedre seg.</p>

<form class="m-t" role="form" method="POST" action="{{ route('ratings.feedback.store', ['hash' => $hash, 'rate' => $rate]) }}">
    {{ csrf_field() }}

    {{-- Feedback --}}
    <div class="form-group">
        <textarea name="feedback" id="feedback" cols="30" rows="5" required>{{ old('feedback') }}</textarea>
    </div>

    {{-- Submit --}}
    <button type="submit" class="btn btn-primary block full-width m-b">Send inn</button>
</form>
@endsection