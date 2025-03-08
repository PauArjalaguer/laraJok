@extends('layout.mainlayout')
@section('title',"Pavellons :: JOK.cat ")
@section('content')

@section('name', 'content')
    @php
        $dia = '';
    @endphp
@foreach($partits_pavello as $key => $match)
    @if($dia != $match->matchDate)
     <b>{{ \Carbon\Carbon::parse($match->matchDate)->locale('ca')->isoFormat('LL') }}</b>
    @endif
    <x-matches-component :match="$match" />
    @php
        $dia = $match->matchDate;
    @endphp
@endforeach
@endsection
