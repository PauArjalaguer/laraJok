@extends('layout.mainlayout')
@section('title',"Agenda :: JOK.cat ")
@section('content')
<div class="w-full mt-2 mb-4">
    <div class="flex items-center justify-between border-b border-neutral-200 pb-3">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800 font-['Comfortaa']">
                <i class="fa-solid fa-calendar-days text-neutral-500 mr-2"></i>Agenda
            </h1>
            <p class="text-sm text-neutral-500 mt-0.5">Tots els partits programats</p>
        </div>
    </div>
</div>
<input 
type="text" 
id="searchInput" 
placeholder="Buscar a l' agenda..." 
onkeyup="filterTable()" 
style="margin-bottom: 10px; padding: 8px; width: 100%; border: 1px solid #999;"
>
<table class="w-full border-collapse bg-white shadow-sm" id="agenda">
    <thead>
        <tr class="bg-neutral-700">
          <th class="border border-gray-200 px-3 py-4 text-left text-sm font-semibold text-gray-50">Data</th>
          <th class="border border-gray-200 px-3 py-4 text-left text-sm font-semibold text-gray-50">Competició</th>     
          <th class="border border-gray-200 px-3 py-4 text-left text-sm font-semibold text-gray-50"><span class='hidden md:inline'>Jornada</span><span class='inline md:hidden'>Jor.</span></th>
          <th class="border border-gray-200 px-3 py-4 text-left text-sm font-semibold text-gray-50">Local</th>       
          <th class="border border-gray-200 px-3 py-4 text-left text-sm font-semibold text-gray-50" colspan=2>&nbsp;</th>
          <th class="border border-gray-200 px-3 py-4 text-left text-sm font-semibold text-gray-50">Visitant</th>
        
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
@foreach($agenda as $match)
    <tr class="hover:bg-neutral-50">
        <td class="border border-gray-200 px-3 py-4 text-sm text-gray-900 text-xs md:text-base whitespace-nowrap">{{ \Carbon\Carbon::parse($match->matchDate)->format('d-m-y')}} {{\Carbon\Carbon::parse($match->matchHour)->format('H:i')}}</td>
        <td class="border border-gray-200 px-3 py-4 text-sm text-gray-900 text-xs md:text-base"> <a class="active:text-neutral-300" href="/competicio/{{$match->idGroup}}/{{urlencode($match->groupName)}}">{{$match->groupName}}</a></td>
        <td class="border border-gray-200 px-3 py-4 text-sm text-gray-900 text-xs md:text-base text-center">{{$match->idRound}}</td>  
        <td class="border border-gray-200 px-3 py-4 text-sm text-gray-900 text-xs md:text-base"><a class="active:text-neutral-300" href="/equip/{{$match->localTeamId}}/{{urlencode($match->localTeamName)}}">{{$match->localTeamName}}</a></td>
        <td class="border border-gray-200 px-3 py-4 text-sm text-gray-900 text-xs md:text-base text-center">{{$match->localResult}}</td>      
        <td class="border border-gray-200 px-3 py-4 text-sm text-gray-900 text-xs md:text-base text-center">{{$match->visitorResult}}</td> 
         <td class="border border-gray-200 px-3 py-4 text-sm text-gray-900 text-xs md:text-base"><a class="active:text-neutral-300" href="/equip/{{$match->visitorTeamId}}/{{urlencode($match->visitorTeamName)}}">{{$match->visitorTeamName}}</td>
       

    
    </tr>
@endforeach
</tbody>
</table>
<script>
  function filterTable() {
      // Obtener el valor del input de búsqueda
      const searchValue = document.getElementById('searchInput').value.toLowerCase();

      // Obtener todas las filas de la tabla
      const rows = document.querySelectorAll('#agenda tbody tr');

      // Iterar sobre cada fila
      rows.forEach(row => {
          const cells = row.querySelectorAll('td');
          const rowText = Array.from(cells).map(cell => cell.textContent.toLowerCase()).join(' ');

          // Mostrar u ocultar la fila según coincida con el texto de búsqueda
          if (rowText.includes(searchValue)) {
              row.classList.remove('hidden');
          } else {
              row.classList.add('hidden');
          }
      });
  }
</script>
@endsection