@extends('layout.mainlayout')
@section('title',"Agenda :: JOK.cat ")
@section('content')

<table class="w-full border-collapse bg-white shadow-sm">
    <thead>
        <tr class="bg-neutral-700">
          <th class="border border-gray-200 px-3 py-4 text-left text-sm font-semibold text-gray-50">Data</th>
          <th class="border border-gray-200 px-3 py-4 text-left text-sm font-semibold text-gray-50">Competició</th>     
          <th class="border border-gray-200 px-3 py-4 text-left text-sm font-semibold text-gray-50">Jornada</th>
          <th class="border border-gray-200 px-3 py-4 text-left text-sm font-semibold text-gray-50">Local</th>       
          <th class="border border-gray-200 px-3 py-4 text-left text-sm font-semibold text-gray-50" colspan=2>&nbsp;</th>
          <th class="border border-gray-200 px-3 py-4 text-left text-sm font-semibold text-gray-50">Visitant</th>
        
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
@foreach($agenda as $match)
    <tr class="hover:bg-neutral-50">
        <td class="border border-gray-200 px-3 py-4 text-sm text-gray-900">{{ \Carbon\Carbon::parse($match->matchDate)->format('d-m-y')}} {{\Carbon\Carbon::parse($match->matchHour)->format('H:i')}}</td>
        <td class="border border-gray-200 px-3 py-4 text-sm text-gray-900"> <a href="/competicio/{{$match->idGroup}}/{{urlencode($match->groupName)}}">{{$match->groupName}}</a></td>
        <td class="border border-gray-200 px-3 py-4 text-sm text-gray-900 text-center">{{$match->idRound}}</td>  
        <td class="border border-gray-200 px-3 py-4 text-sm text-gray-900"><a href="/equip/{{$match->localTeamId}}/{{urlencode($match->localTeamName)}}">{{$match->localTeamName}}</a></td>
        <td class="border border-gray-200 px-3 py-4 text-sm text-gray-900 text-center">{{$match->localResult}}</td>      
        <td class="border border-gray-200 px-3 py-4 text-sm text-gray-900 text-center">{{$match->visitorResult}}</td> 
         <td class="border border-gray-200 px-3 py-4 text-sm text-gray-900"><a href="/equip/{{$match->visitorTeamId}}/{{urlencode($match->visitorTeamName)}}">{{$match->visitorTeamName}}</td>
       

    
    </tr>
@endforeach
</tbody>
</table>

@endsection