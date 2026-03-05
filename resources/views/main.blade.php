@extends('layout.mainlayout')
@section('title'," JOK.cat ")
@section('content')
   <script async src="https://tally.so/widgets/embed.js"></script>
    <script>
        window.TallyConfig = {
        "formId": "EkPjzA",
        "popup": {
            "emoji": {
            "text": "👋",
            "animation": "flash"
            },
            "hideTitle": true,
            "autoClose": 0,
            "formEventsForwarding": true,
            "open": {
            "trigger": "scroll",
            "scrollPercent": 10
            }
        }
        };
    </script>
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
        @foreach($matchesListLastWithResults as $match)
        <x-matches-component :match="$match" />
        @endforeach
    </div>
</div>
@endsection
