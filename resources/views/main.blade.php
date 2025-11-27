@extends('layout.mainlayout')
@section('title'," JOK.cat ")
@section('content')

<x-slider-component :newsListTop="$newsListTop" />
<div class="w-full lg:flex my-2">
    <div class="lg:w-1/2 pr-1">
        <div class="block mb-2">
            <h1 class="font-bold text-xl text-neutral-700">Propers partits</h1>
        </div>
        @foreach($matchesListNext as $match)
        <x-matches-component :match="$match" />
        @endforeach
    </div>
    <div class="lg:w-1/2 pl-1">
        <div class="block mb-2">
            <h1 class="font-bold text-xl text-neutral-700">Darrers resultats</h1>
        </div>
        @php
            $limit = max(count($matchesListNext), 5);
        @endphp
        @foreach($matchesListLastWithResults as $k => $match)
            @if($k >= $limit) @break @endif
        <x-matches-component :match="$match" />
        @endforeach
    </div>
</div>
@endsection
