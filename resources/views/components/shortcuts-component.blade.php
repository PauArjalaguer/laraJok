@if(count($userSavedData) > 0)
<div class="w-full flex items-center gap-2 overflow-x-auto pb-2 mb-4 scrollbar-none font-display">
    @foreach($userSavedData as $userData)
        @php
            if ($userData->category == 'club') {
                $label = $userData->clubName;
            } else if ($userData->category == 'equip') {
                $label = $userData->teamName;
            } else if ($userData->category == 'competicio') {
                $label = $userData->groupName;
            } else if ($userData->category == 'jugador') {
                $label = $userData->playerName;
            } else {
                $label = "--";
            }
        @endphp
        <a href="/{{ $userData->category }}/{{ $userData->idItem }}/{{ urlencode($label) }}" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-stone-100 dark:bg-[#121215] border border-stone-200/80 dark:border-stone-800 text-xs font-black text-stone-800 dark:text-stone-200 hover:border-primary dark:hover:border-stone-600 hover:text-stone-900 dark:hover:text-white transition-all shadow-xs flex-shrink-0 group">
            @if ($userData->category == 'jugador')
                <i class="fa-regular fa-user text-stone-400 dark:text-stone-500 group-hover:text-stone-900 dark:hover:text-white text-[11px] transition-colors"></i>
            @elseif ($userData->category == 'equip')
                <i class="fa-solid fa-users text-stone-400 dark:text-stone-500 group-hover:text-stone-900 dark:hover:text-white text-[11px] transition-colors"></i>
            @elseif ($userData->category == 'competicio')
                <i class="fa-solid fa-trophy text-stone-400 dark:text-stone-500 group-hover:text-stone-900 dark:hover:text-white text-[11px] transition-colors"></i>
            @elseif ($userData->category == 'club')
                <i class="fa-solid fa-shield text-stone-400 dark:text-stone-500 group-hover:text-stone-900 dark:hover:text-white text-[11px] transition-colors"></i>
            @endif
            <span>{{ App\Http\Controllers\TeamsController::teamFormat($label) }}</span>
        </a>
    @endforeach
</div>
@endif