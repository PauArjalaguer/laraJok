<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;900&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/color-thief/2.3.0/color-thief.umd.js"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; width: 1920px; height: 960px; overflow-y: auto; overflow-x: hidden; }
        .glass-panel {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }
    </style>
</head>
<body class="flex flex-col items-center justify-between p-6 text-white relative transition-colors duration-1000 ease-in-out" id="mainBody">
    
    {{-- Dynamic Background Gradient --}}
    <div class="absolute top-0 left-0 w-full h-full z-0 opacity-100 pointer-events-none transition-all duration-1000" id="bgGradient" style="background: linear-gradient(135deg, #1e3a8a 0%, #000000 100%);"></div>
    
    {{-- Decorative Elements --}}
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 opacity-20 pointer-events-none">
        <div class="absolute -top-20 -left-20 w-96 h-96 bg-white rounded-full blur-3xl mix-blend-overlay"></div>
        <div class="absolute bottom-0 right-0 w-[800px] h-[800px] bg-white rounded-full blur-3xl mix-blend-overlay opacity-50"></div>
    </div>

    {{-- Header --}}
  <!--   <div class="w-full flex justify-between items-center mb-8 z-10">
        <div class="flex items-center gap-6">
            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center p-2 shadow-lg relative overflow-hidden">
                <img src="{{ $clubInfo->clubImage }}" id="clubLogo" crossorigin="anonymous" class="w-full h-full object-contain" onload="extractColors()">
            </div>
            <div class="flex flex-col">
                <h2 class="text-4xl font-bold text-white/90">{{ $clubInfo->clubName }}</h2>
                <span class="text-xl text-white/70 uppercase tracking-widest">Resultats Setmanals</span>
            </div>
        </div> -->
     <!--    <div class="text-center">
            <h1 class="text-8xl font-black uppercase tracking-tighter drop-shadow-lg leading-none">JORNADA</h1>            
        </div> -->
       
    </div>

    {{-- Matches Container --}}
    <div class="w-full max-w-[1800px] z-10 flex-grow flex flex-col justify-center">
        @php
            $matchCount = count($matches);
            $gridCols = $matchCount > 6 ? 'grid-cols-2' : 'grid-cols-1';
            $gap = $matchCount > 6 ? 'gap-5' : 'gap-4';
        @endphp

        <div class="grid {{ $gridCols }} {{ $gap }} w-full">
            @foreach($matches as $index => $matchData)
            @if(isset($matchData[0]))
                @php $m = $matchData[0]; @endphp
                <div class="glass-panel rounded-xl overflow-hidden flex flex-col">
                    {{-- Match Header (Category/Group) --}}
                    <div class="bg-black/20 px-4 py-2 text-center text-sm font-bold uppercase tracking-wider text-white/70 flex justify-between items-center">
                        <span>{{ $m->groupName }}</span>
                        <span class="bg-white/20 px-2 py-0.5 rounded text-xs">J{{ $m->idRound }}</span>
                    </div>

                    {{-- Match Content --}}
                    <div class="flex items-center justify-between p-4 h-24 relative">
                        {{-- Local --}}
                        <div class="flex items-center w-5/12 justify-end gap-4">
                            <span class="text-xl font-bold text-right truncate text-white drop-shadow-sm leading-tight">{{ $m->teamName }}</span>
                            <img src="{{ $m->clubImage1 }}" class="w-16 h-16 object-contain bg-white/10 rounded-full p-1 shadow-sm backdrop-blur-sm">
                        </div>

                        {{-- Result --}}
                        <div class="flex items-center justify-center w-2/12 gap-2 z-10">
                            <span class="text-4xl font-black text-white drop-shadow-md">{{ $m->localResult }}</span>
                            <span class="text-2xl text-white/50 font-bold">-</span>
                            <span class="text-4xl font-black text-white drop-shadow-md">{{ $m->visitorResult }}</span>
                        </div>

                        {{-- Visitor --}}
                        <div class="flex items-center w-5/12 justify-start gap-4">
                            <img src="{{ $m->clubImage2 }}" class="w-12 h-16 object-contain bg-white/10 rounded-full p-1 shadow-sm backdrop-blur-sm">
                            <span class="text-xl font-bold text-left truncate text-white drop-shadow-sm leading-tight">{{ $m->teamName2 }}</span>
                        </div>
                    </div>
                </div>
            @endif
            @endforeach
        </div>
    </div>

    {{-- Footer --}}
    <div class="w-full flex justify-between items-end mt-12 z-10 border-t border-white/10 pt-6">
        <div class="flex gap-4 items-center">
             <span class="text-2xl font-bold text-white/80">#somJOK</span>
        </div>
        <div class="text-2xl font-bold text-white/60">www.jok.cat</div>
    </div>

    <script>
        function extractColors() {
            const img = document.getElementById('clubLogo');
            const colorThief = new ColorThief();
            
            // Make sure image is loaded
            if (img.complete) {
                applyColors();
            } else {
                img.addEventListener('load', function() {
                    applyColors();
                });
            }

            function applyColors() {
                try {
                    const dominantColor = colorThief.getColor(img);
                    const palette = colorThief.getPalette(img, 2);
                    
                    const color1 = `rgb(${dominantColor[0]}, ${dominantColor[1]}, ${dominantColor[2]})`;
                    const color2 = palette[1] ? `rgb(${palette[1][0]}, ${palette[1][1]}, ${palette[1][2]})` : '#000000';
                    
                    // Create a rich gradient
                    const gradient = `linear-gradient(135deg, ${color1} 0%, ${color2} 100%)`;
                    document.getElementById('bgGradient').style.background = gradient;
                    
                    // Adjust text contrast if needed (simple check)
                    // For now, we assume logos are generally colorful/dark enough for white text, 
                    // or we use text-shadows which we already have.
                    
                } catch (e) {
                    console.error("Error extracting colors", e);
                }
            }
        }
    </script>
</body>
</html>
