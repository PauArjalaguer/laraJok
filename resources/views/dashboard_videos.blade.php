<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gestió de Vídeos i Canals de YouTube
            </h2>
            <form action="{{ route('dashboard.videos.sync') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md font-bold text-xs uppercase tracking-wider flex items-center gap-2 shadow-sm transition-all cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    <span>Sincronitzar Vídeos Ara</span>
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('status'))
                <div class="bg-green-500 rounded-md p-4 text-white font-bold shadow-sm flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Form: Add Channel / Playlist -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-indigo-600">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Afegir Canal o Llista de Reproducció de YouTube</span>
                </h3>

                <form action="{{ route('dashboard.videos.channel.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nom del Canal / Llista</label>
                        <input type="text" name="name" required placeholder="p.ex. Club Hoquei Prat" class="w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tipus</label>
                        <select name="type" required class="w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="channel">Canal (@handle / UC...)</option>
                            <option value="playlist">Llista de reproducció (PL...)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Identificador / Handle / ID</label>
                        <input type="text" name="identifier" required placeholder="@ClubHoqueiPrat o PL_..." class="w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full py-2 px-4 bg-gray-900 hover:bg-black text-white text-xs font-bold uppercase rounded-md shadow-xs transition-all">
                            Desar i Sincronitzar
                        </button>
                    </div>
                </form>
            </div>

            <!-- Table: Configured Channels -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-bold text-gray-900 mb-4">Canals i Llistes Configurats ({{ $channels->count() }})</h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Nom</th>
                                <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Tipus</th>
                                <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Identificador</th>
                                <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Vídeos Importats</th>
                                <th class="px-4 py-3 text-right font-bold text-gray-500 uppercase tracking-wider">Accions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($channels as $channel)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-bold text-gray-900">{{ $channel->name }}</td>
                                    <td class="px-4 py-3 uppercase text-gray-500 font-semibold">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] {{ $channel->type === 'playlist' ? 'bg-purple-100 text-purple-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $channel->type }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 font-mono">{{ $channel->identifier }}</td>
                                    <td class="px-4 py-3 font-bold text-indigo-600">{{ $channel->videos_count }} vídeos</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('dashboard.videos.channel.delete', $channel->id) }}" 
                                           onclick="return confirm('Segur que vols eliminar aquest canal i tots els seus vídeos?')"
                                           class="text-red-600 hover:text-red-900 font-bold">
                                            Eliminar
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Table: Recent Videos -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-bold text-gray-900 mb-4">Darrers Vídeos Importats ({{ $recentVideos->total() }})</h3>

                <div class="space-y-3">
                    @foreach($recentVideos as $video)
                        <div class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50 transition-all gap-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <img src="{{ $video->thumbnail_url }}" alt="" class="w-16 h-10 object-cover rounded flex-shrink-0 bg-gray-200" />
                                <div class="min-w-0">
                                    <a href="{{ $video->url }}" target="_blank" class="text-xs font-bold text-gray-900 hover:text-indigo-600 truncate block">
                                        {{ $video->title }}
                                    </a>
                                    <div class="text-[11px] text-gray-500 flex items-center gap-2">
                                        <span>{{ $video->channel->name ?? 'Sense canal' }}</span>
                                        <span>•</span>
                                        <span>{{ $video->published_at ? $video->published_at->format('d/m/Y H:i') : '' }}</span>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ $video->url }}" target="_blank" class="text-xs font-bold text-indigo-600 hover:underline flex-shrink-0">
                                Veure a YouTube ↗
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $recentVideos->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
