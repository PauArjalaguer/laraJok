@php use Carbon\Carbon; $agenda = collect($agenda); @endphp
@extends('layout.mainlayout')
@section('title',"Calendari :: JOK.cat ")
@section('content')
@php
    Carbon::setLocale('ca');
    $any_actual = Carbon::now()->year;
    $any_seguent = Carbon::now()->year+1;
    $mes_actual = Carbon::now()->month;
@endphp

<div class="w-full mx-auto mt-10">
@for($any=$any_actual; $any<=$any_seguent; $any++)
    <h1 class="text-neutral-700 text-3xl font-bold mb-6">{{$any}}</h1>

    @for($mes =$mes_actual; $mes<=12; $mes++)
        @php
            $primer_dia = Carbon::create($any, $mes, 1);
            $nom_mes = ucfirst(strtolower($primer_dia->isoFormat('MMMM')));
            $num_primer_dia = $primer_dia->dayOfWeek;
            $dies_del_mes = $primer_dia->daysInMonth;
            $dies = 0;
            $compta_setmanes = 0;
        @endphp

        <div class="mb-10">
            <h2 class="text-xl font-semibold text-gray-700 mb-3">{{$nom_mes}}</h2>

            <div class="grid grid-cols-1 md:grid-cols-7 border border-gray-300 rounded-lg overflow-hidden">
                @for($a=1; $a<$num_primer_dia; $a++)
                    <div class="hidden md:block h-48 bg-gray-50"></div>
                    @php $dies++; $compta_setmanes++; @endphp
                @endfor

                @for($a=1; $a<=$dies_del_mes; $a++)
                    @php
                        $dies++;
                        $compta_setmanes++;
                        $dia_del_mes = $dies - $num_primer_dia + 1;
                        $dateObj = Carbon::create($any,$mes,$dia_del_mes);
                        $partits_del_dia = $agenda->where('matchDate', $dateObj->format("Y-m-d"));
                        $isToday = $dateObj->isToday();
                    @endphp

                    <div {{ $isToday ? 'id=today' : '' }} class="min-h-[8rem] md:h-48 border border-gray-200 p-2 flex flex-col text-sm {{ $isToday ? 'bg-blue-50 ring-2 ring-blue-300' : '' }}">
                        <div class="flex justify-end">
                            <div class="w-7 h-7 rounded-full bg-neutral-700 text-white flex items-center justify-center text-xs font-bold shadow">
                                {{$dia_del_mes}}
                            </div>
                        </div>

                        <div class="mt-1 space-y-1 overflow-y-auto pr-1">
                            @foreach($partits_del_dia as $partit)
                                <div class="text-[11px] bg-white border border-gray-200 rounded p-1 shadow-sm hover:bg-gray-100 transition">
                                    <span class="font-semibold">{{substr($partit->matchHour,0,5)}}</span>
                                    {{ Str::title(strtolower($partit->localTeam)) }} - {{ Str::title(strtolower($partit->visitorTeam)) }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        @if($any==$any_actual && $mes==11)
            @php $mes_actual = 1; @endphp
        @endif
    @endfor
@endfor
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (window.innerWidth < 768) {
            const today = document.getElementById('today');
            if (today) {
                setTimeout(() => {
                    today.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 100);
            }
        }
    });
</script>
@endsection