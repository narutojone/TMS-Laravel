@extends('layouts.plain')

@section('title', 'Accounting Group AS')

@section('content')
<style>
.checkmark {
  width: 200px;
  margin: 0 auto;
  padding-top: 40px;
}

.path {
  stroke-dasharray: 1000;
  stroke-dashoffset: 0;
  animation: dash 2s ease-in-out;
  -webkit-animation: dash 2s ease-in-out;
}

.spin {
  animation: spin 2s;
  -webkit-animation: spin 2s;
  transform-origin: 50% 50%;
  -webkit-transform-origin: 50% 50%;
}

@-webkit-keyframes dash {
 0% {
   stroke-dashoffset: 1000;
 }
 100% {
   stroke-dashoffset: 0;
 }
}

@keyframes dash {
 0% {
   stroke-dashoffset: 1000;
 }
 100% {
   stroke-dashoffset: 0;
 }
}

@-webkit-keyframes spin {
  0% {
    -webkit-transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
  }
}

@keyframes spin {
  0% {
    -webkit-transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
  }
}

@-webkit-keyframes text {
  0% {
    opacity: 0; }
  100% {
    opacity: 1;
  }

  
  @keyframes text {
  0% {
    opacity: 0; }
  100% {
    opacity: 1;
  }
}
</style>
<div class="checkmark">
	<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 161.2 161.2" enable-background="new 0 0 161.2 161.2" xml:space="preserve">
		<path class="path" fill="none" stroke="#00cc00" stroke-miterlimit="10" d="M425.9,52.1L425.9,52.1c-2.2-2.6-6-2.6-8.3-0.1l-42.7,46.2l-14.3-16.4c-2.3-2.7-6.2-2.7-8.6-0.1c-1.9,2.1-2,5.6-0.1,7.7l17.6,20.3c0.2,0.3,0.4,0.6,0.6,0.9c1.8,2,4.4,2.5,6.6,1.4c0.7-0.3,1.4-0.8,2-1.5c0.3-0.3,0.5-0.6,0.7-0.9l46.3-50.1C427.7,57.5,427.7,54.2,425.9,52.1z"/>
		<circle class="path" fill="none" stroke="#00cc00" stroke-width="4" stroke-miterlimit="10" cx="80.6" cy="80.6" r="62.1"/>
		<polyline class="path" fill="none" stroke="#00cc00" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" points="113,52.8 74.1,108.4 48.2,86.4 "/>
	</svg>
</div>
<h2 style="font-weight: bold;font-size: 32px;line-height: 42px;">Takk!</h2>
<p style="font-size: 16px;line-height: 23px;color: #616161;mso-line-height-rule: exactly;">Vi har nå mottatt din tilbakemelding. Du kan trygt lukke denne siden.</p>
@endsection
