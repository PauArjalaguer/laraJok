@extends('layout.mainlayout')
@section('content')

@php
if (count($userSavedData)>0){
echo ' <h1 class="font-bold text-xl text-slate-700">Accesos directes</h1>';
}
@endphp
<div class="w-full flex my-4">
    @foreach($userSavedData as $userData)
    @php
    $label =$userData->groupName.$userData->teamName.$userData->playerName;
    @endphp
    <div class='p-3 bg-slate-200  inline  rounded-xl mr-1'><a href="/{{$userData->category}}/{{$userData->idItem}}/{{$label}}">
            @if ($userData->category=='jugador')
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 inline">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
            </svg>
            @endif

            @if ($userData->category=='equip')
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 inline">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
            </svg>

            @endif
            @if ($userData->category=='competicio')
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 inline">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
            </svg>@endif

            {{$label}}</a></div>
    @endforeach
</div>

<div class="w-full lg:flex">
    <div class="lg:w-1/2 pr-1">
        <div class="block mb-2">
            <h1 class="font-bold text-xl text-slate-700">Propers partits</h1>
        </div>
        @foreach($matchesListNext as $match)
        <x-matches-component :matchArray="$match" />
        @endforeach
    </div>
    <div class="lg:w-1/2 pl-1">
        <div class="block mb-2">
            <h1 class="font-bold text-xl text-slate-700">Propers partits</h1>
        </div>
        @foreach($matchesListLastWithResults as $match)
        <x-matches-component :matchArray="$match" />
        @endforeach
    </div>

</div>


@endsection