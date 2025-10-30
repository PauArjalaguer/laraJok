@php use Carbon\Carbon;
$agenda = collect($agenda);
@endphp
@extends('layout.mainlayout')
@section('title',"Calendari :: JOK.cat ")
@section('content')
    @php
        Carbon::setLocale('ca');
            $any_actual = Carbon::now()->year;
            $any_seguent = Carbon::now()->year+1;
            $mes_actual = Carbon::now()->month;
    @endphp

    @for($any=$any_actual; $any<=$any_seguent;$any++)
        <h1 class="text-slate-600 mt-10 font-bold">{{$any}}</h1>
        @for($mes =$mes_actual;$mes<=12;$mes++)
            @php
                $primer_dia = Carbon::create($any, $mes, 1);
                $nom_mes = $primer_dia->isoFormat('MMMM');
                $num_primer_dia= $primer_dia->dayOfWeek;
                $dies_del_mes = $primer_dia->daysInMonth;

                $dies =0;
                $compta_setmanes=0;
            @endphp
            <table width="100%" style="table-layout:fixed;   /* reparteix l’amplada per igual */
    border-collapse:collapse;">
                <tr><td>&nbsp;</td></tr>
                <tr>
                    <th colspan="7" align="left">{{ucfirst(strtolower($nom_mes))}} </th>
                </tr>
                <tr>
                    @for($a=1;$a<$num_primer_dia;$a++)
                        <td style=" width:14%;">&nbsp;</td>
                        @php
                            $dies ++;
                            $compta_setmanes++;
                        @endphp
                    @endfor
                    @for($a=1;$a<$dies_del_mes;$a++)
                            @php
                                $dies ++;
                                $compta_setmanes++;
                                $dia_del_mes = $dies  -$num_primer_dia +1 ;
                                $partits_del_dia = $agenda->where('matchDate',  Carbon::create($any, $mes, $dia_del_mes)->format("Y-m-d"));
                                //print_r($partits_del_dia);
                            @endphp

                            <td style="border:1px solid #e5e5e5; vertical-align: top; height: 200px; padding:0px;" class="border-neutral-400">
                                <div class="grid place-items-center min-h-30 ">
                                    <div class="m-2 w-6 h-6 rounded-full bg-neutral-600 text-white flex items-center justify-center  text-[9px] font-bold shadow-md">
                                        {{ $dia_del_mes}}
                                    </div>
                                    @foreach($partits_del_dia as $partit)
                                         <div class=" text-[10px] items-center border-t border-neutral-200 p-2">
                                             {{substr($partit->matchHour,0,5)}} {{\Illuminate\Support\Str::title(strtolower($partit->localTeam))}} - {{\Illuminate\Support\Str::title(strtolower($partit->visitorTeam))}}
                                       </div>
                                    @endforeach
                            </td>

    @if($compta_setmanes==7)
           @php $compta_setmanes=0; @endphp
            </tr><tr>
    @endif
@endfor
</tr>
</table>

@if($any==$any_actual && $mes==11)
{{ $mes_actual = 1}}
@endif
@endfor
@endfor
@endsection
