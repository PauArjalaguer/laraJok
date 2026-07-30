<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="apple-mobile-web-app-status-bar" content="#ffffff">
    <meta name="apple-mobile-web-app-capable" content="yes">

    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
            window.dispatchEvent(new Event('theme-changed'));
        }
    </script>
    @vite('resources/css/app.css')
    <title>@yield('title')</title>
    <link rel="manifest" href="/manifest.json">
    <!-- Fonts (Plus Jakarta Sans & Inter per a disseny ultra-modern i Comfortaa per al logo) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@400;700&family=Inter:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@500;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="shortcut icon" href="/favicon.ico">

    <link rel="apple-touch-icon" sizes="16x16" href="/pwa/icons/ios/16.png">
    <link rel="apple-touch-icon" sizes="20x20" href="/pwa/icons/ios/20.png">
    <link rel="apple-touch-icon" sizes="29x29" href="/pwa/icons/ios/29.png">
    <link rel="apple-touch-icon" sizes="32x32" href="/pwa/icons/ios/32.png">
    <link rel="apple-touch-icon" sizes="40x40" href="/pwa/icons/ios/40.png">
    <link rel="apple-touch-icon" sizes="50x50" href="/pwa/icons/ios/50.png">
    <link rel="apple-touch-icon" sizes="57x57" href="/pwa/icons/ios/57.png">
    <link rel="apple-touch-icon" sizes="58x58" href="/pwa/icons/ios/58.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/pwa/icons/ios/60.png">

    <link rel="apple-touch-icon" sizes="64x64" href="/pwa/icons/ios/64.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/pwa/icons/ios/72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/pwa/icons/ios/76.png">
    <link rel="apple-touch-icon" sizes="80x80" href="/pwa/icons/ios/80.png">
    <link rel="apple-touch-icon" sizes="87x87" href="/pwa/icons/ios/87.png">
    <link rel="apple-touch-icon" sizes="100x100" href="/pwa/icons/ios/100.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/pwa/icons/ios/114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/pwa/icons/ios/120.png">
    <link rel="apple-touch-icon" sizes="128x128" href="/pwa/icons/ios/128.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/pwa/icons/ios/144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/pwa/icons/ios/152.png">
    <link rel="apple-touch-icon" sizes="167x167" href="/pwa/icons/ios/167.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/pwa/icons/ios/180.png">
    <link rel="apple-touch-icon" sizes="192x192" href="/pwa/icons/ios/192.png">
    <link rel="apple-touch-icon" sizes="256x256" href="/pwa/icons/ios/256.png">
    <link rel="apple-touch-icon" sizes="512x512" href="/pwa/icons/ios/512.png">
    <link rel="apple-touch-icon" sizes="1024x1024" href="/pwa/icons/ios/1024.png">

    <link href="/pwa/icons/ios/1024.png" sizes="1024x1024" rel="apple-touch-startup-image">
    <link href="/pwa/icons/ios/512.png" sizes="512x512" rel="apple-touch-startup-image">
    <link href="/pwa/icons/ios/256.png" sizes="256x256" rel="apple-touch-startup-image">
    <link href="/pwa/icons/ios/192.png" sizes="192x192" rel="apple-touch-startup-image">
 



    <link rel="manifest" href="/site.webmanifest">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script type="text/javascript">
        (function(c, l, a, r, i, t, y) {
            c[a] = c[a] || function() {
                (c[a].q = c[a].q || []).push(arguments)
            };
            t = l.createElement(r);
            t.async = 1;
            t.src = "https://www.clarity.ms/tag/" + i;
            y = l.getElementsByTagName(r)[0];
            y.parentNode.insertBefore(t, y);
        })(window, document, "clarity", "script", "qjlnn16w4a");

        function predict(id_match){
            fetch(`/api/matches/predict/${id_match}`)
                .then(response => {
                    if (!response.ok) throw new Error('Error en la resposta');
                    return response.text(); // o .json() si el backend retorna JSON
                })
                .then(data => {
                    const el = document.getElementById(`predict_${id_match}`);
                    if (el) el.innerHTML = data;
                })
                .catch(error => {
                    console.error('Error en el fetch:', error);
                    document.getElementById(`predict_${id_match}`).innerHTML('');
            })
        }
    </script>
    <style>
        @keyframes grow-bar {
            from { width: 0; }
            to { width: var(--bar-width); }
        }
        .animate-grow {
            animation: grow-bar 1s ease-out forwards;
        }

        /* ── View Transitions ── */
        @view-transition {
            navigation: auto;
        }
        /* Transició de sortida: fade + puja lleugerament */
        ::view-transition-old(root) {
            animation: 220ms cubic-bezier(0.4, 0, 1, 1) both vt-fade-out;
        }
        /* Transició d'entrada: fade + baixa des de dalt */
        ::view-transition-new(root) {
            animation: 340ms cubic-bezier(0, 0, 0.2, 1) 60ms both vt-fade-in;
        }
        @keyframes vt-fade-out {
            from { opacity: 1; transform: translateY(0); }
            to   { opacity: 0; transform: translateY(-10px); }
        }
        @keyframes vt-fade-in {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        /* Element compartit: imatge hero de la card → detall */
        ::view-transition-old(anunci-hero) {
            animation: 380ms cubic-bezier(0.4, 0, 0.2, 1) both vt-hero-out;
        }
        ::view-transition-new(anunci-hero) {
            animation: 380ms cubic-bezier(0.4, 0, 0.2, 1) both vt-hero-in;
        }
        @keyframes vt-hero-out {
            from { opacity: 1; transform: scale(1); }
            to   { opacity: 0; transform: scale(1.04); }
        }
        /* ── Apple Sports Utility Classes ── */
        .font-display { font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; }
        .font-syne { font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; }
        .font-mono-score { font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; }
        
        .hallmark-stamp {
            display: inline-flex;
            align-items: center;
            padding: 0.22rem 0.65rem;
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            font-size: 0.62rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            border-radius: 9999px;
        }
        .hallmark-grid-bg {
            background-image: radial-gradient(rgba(212, 255, 0, 0.12) 1.5px, transparent 1.5px);
            background-size: 16px 16px;
        }
        </style>
</head>

<body class="antialiased bg-white text-stone-900 dark:bg-[#09090b] dark:text-stone-100 font-display transition-colors duration-300 min-h-screen">



    <div class="relative">
        <!-- Sidebar Backdrop Overlay -->
        <div id="sidebarBackdrop" onclick="toggleMenu()" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-40 hidden transition-opacity duration-300"></div>

        <!-- Sidebar Drawer -->
        <div id="sidebar" class="fixed left-0 top-0 w-[80%] sm:w-[320px] max-w-sm h-full bg-white dark:bg-[#121215] text-stone-900 dark:text-white border-r border-stone-200 dark:border-stone-800/90 z-50 -translate-x-full transition-transform duration-300 ease-in-out shadow-2xl p-5 flex flex-col justify-between overflow-y-auto font-display">
            <div>
                <!-- Header -->
                <div class="flex items-center justify-between pb-4 border-b border-stone-200 dark:border-stone-800 mb-4">
                    <a href="/" class="text-2xl font-black tracking-tight text-stone-900 dark:text-white">
                        JOK<span class="text-[#d4ff00]">.cat</span>
                    </a>
                    <button onclick="toggleMenu()" class="w-8 h-8 rounded-full bg-stone-100 dark:bg-stone-900 text-stone-600 dark:text-stone-300 hover:bg-[#d4ff00] hover:text-black dark:hover:bg-[#d4ff00] dark:hover:text-black transition-colors flex items-center justify-center">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <!-- Live Search inside Sidebar -->
                <div class="relative mb-5">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-stone-400 text-xs"></i>
                    <input type="text" placeholder="Cerca equip o jugador..." spellcheck="false" autocomplete="off" class="w-full pl-9 pr-4 py-2.5 rounded-full bg-stone-100 dark:bg-stone-900 text-stone-900 dark:text-white border border-stone-200 dark:border-stone-800 focus:outline-none focus:border-[#d4ff00] text-xs font-medium transition-colors" onkeyup="search(this.value)" />
                </div>

                <div id="sidebarSearchResults" class="text-xs font-bold text-stone-500 dark:text-stone-400 text-center mb-3 hidden"></div>

                <!-- Navigation List -->
                <div class="space-y-1">
                    <a href="/" class="group flex items-center justify-between p-2.5 rounded-2xl hover:bg-stone-100 dark:hover:bg-stone-900 transition-colors {{ request()->is('/') ? 'bg-stone-100 dark:bg-stone-900' : '' }}">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-stone-100 dark:bg-stone-900 text-stone-700 dark:text-stone-300 group-hover:bg-[#d4ff00] group-hover:text-black flex items-center justify-center text-xs transition-colors">
                                <i class="fa-solid fa-house"></i>
                            </span>
                            <span class="text-xs font-black uppercase tracking-wider text-stone-800 dark:text-stone-200 group-hover:text-stone-900 dark:group-hover:text-[#d4ff00]">Inici</span>
                        </div>
                        <i class="fa-solid fa-chevron-right text-[10px] text-stone-400 group-hover:translate-x-0.5 transition-transform"></i>
                    </a>

                    <a href="/competicions" class="group flex items-center justify-between p-2.5 rounded-2xl hover:bg-stone-100 dark:hover:bg-stone-900 transition-colors {{ request()->is('competicions*') ? 'bg-stone-100 dark:bg-stone-900' : '' }}">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-stone-100 dark:bg-stone-900 text-stone-700 dark:text-stone-300 group-hover:bg-[#d4ff00] group-hover:text-black flex items-center justify-center text-xs transition-colors">
                                <i class="fa-solid fa-trophy"></i>
                            </span>
                            <span class="text-xs font-black uppercase tracking-wider text-stone-800 dark:text-stone-200 group-hover:text-stone-900 dark:group-hover:text-[#d4ff00]">Competicions</span>
                        </div>
                        <i class="fa-solid fa-chevron-right text-[10px] text-stone-400 group-hover:translate-x-0.5 transition-transform"></i>
                    </a>

                    <a href="/noticies" class="group flex items-center justify-between p-2.5 rounded-2xl hover:bg-stone-100 dark:hover:bg-stone-900 transition-colors {{ request()->is('noticies*') ? 'bg-stone-100 dark:bg-stone-900' : '' }}">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-stone-100 dark:bg-stone-900 text-stone-700 dark:text-stone-300 group-hover:bg-[#d4ff00] group-hover:text-black flex items-center justify-center text-xs transition-colors">
                                <i class="fa-regular fa-newspaper"></i>
                            </span>
                            <span class="text-xs font-black uppercase tracking-wider text-stone-800 dark:text-stone-200 group-hover:text-stone-900 dark:group-hover:text-[#d4ff00]">Notícies</span>
                        </div>
                        <i class="fa-solid fa-chevron-right text-[10px] text-stone-400 group-hover:translate-x-0.5 transition-transform"></i>
                    </a>

                    <a href="/pavellons" class="group flex items-center justify-between p-2.5 rounded-2xl hover:bg-stone-100 dark:hover:bg-stone-900 transition-colors {{ request()->is('pavellons*') ? 'bg-stone-100 dark:bg-stone-900' : '' }}">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-stone-100 dark:bg-stone-900 text-stone-700 dark:text-stone-300 group-hover:bg-[#d4ff00] group-hover:text-black flex items-center justify-center text-xs transition-colors">
                                <i class="fa-solid fa-map-location-dot"></i>
                            </span>
                            <span class="text-xs font-black uppercase tracking-wider text-stone-800 dark:text-stone-200 group-hover:text-stone-900 dark:group-hover:text-[#d4ff00]">Pavellons</span>
                        </div>
                        <i class="fa-solid fa-chevron-right text-[10px] text-stone-400 group-hover:translate-x-0.5 transition-transform"></i>
                    </a>

                    <a href="/agenda" class="group flex items-center justify-between p-2.5 rounded-2xl hover:bg-stone-100 dark:hover:bg-stone-900 transition-colors {{ request()->is('agenda*') ? 'bg-stone-100 dark:bg-stone-900' : '' }}">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-stone-100 dark:bg-stone-900 text-stone-700 dark:text-stone-300 group-hover:bg-[#d4ff00] group-hover:text-black flex items-center justify-center text-xs transition-colors">
                                <i class="fa-regular fa-calendar-days"></i>
                            </span>
                            <span class="text-xs font-black uppercase tracking-wider text-stone-800 dark:text-stone-200 group-hover:text-stone-900 dark:group-hover:text-[#d4ff00]">Agenda</span>
                        </div>
                        <i class="fa-solid fa-chevron-right text-[10px] text-stone-400 group-hover:translate-x-0.5 transition-transform"></i>
                    </a>

                    <a href="/anuncis" class="group flex items-center justify-between p-2.5 rounded-2xl hover:bg-stone-100 dark:hover:bg-stone-900 transition-colors {{ request()->is('anuncis*') ? 'bg-stone-100 dark:bg-stone-900' : '' }}">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-stone-100 dark:bg-stone-900 text-stone-700 dark:text-stone-300 group-hover:bg-[#d4ff00] group-hover:text-black flex items-center justify-center text-xs transition-colors">
                                <i class="fa-solid fa-tags"></i>
                            </span>
                            <span class="text-xs font-black uppercase tracking-wider text-stone-800 dark:text-stone-200 group-hover:text-stone-900 dark:group-hover:text-[#d4ff00]">Segona Mà</span>
                        </div>
                        <span class="bg-[#d4ff00] text-black text-[9px] font-black px-2 py-0.5 rounded-full uppercase shadow-xs">Nou</span>
                    </a>

                    <a href="/merchandising" class="group flex items-center justify-between p-2.5 rounded-2xl hover:bg-stone-100 dark:hover:bg-stone-900 transition-colors {{ request()->is('merchandising*') ? 'bg-stone-100 dark:bg-stone-900' : '' }}">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-stone-100 dark:bg-stone-900 text-stone-700 dark:text-stone-300 group-hover:bg-[#d4ff00] group-hover:text-black flex items-center justify-center text-xs transition-colors">
                                <i class="fa-solid fa-shirt"></i>
                            </span>
                            <span class="text-xs font-black uppercase tracking-wider text-stone-800 dark:text-stone-200 group-hover:text-stone-900 dark:group-hover:text-[#d4ff00]">Botiga</span>
                        </div>
                        <i class="fa-solid fa-chevron-right text-[10px] text-stone-400 group-hover:translate-x-0.5 transition-transform"></i>
                    </a>
                </div>
            </div>

            <!-- Footer User Action -->
            <div class="pt-4 border-t border-stone-200 dark:border-stone-800 mt-6">
                @if (Auth::check())
                    <a href="/dashboard" class="flex items-center justify-between p-3 rounded-2xl bg-stone-100 dark:bg-stone-900 hover:border-[#d4ff00] transition-colors border border-transparent">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-[#d4ff00] text-black font-black flex items-center justify-center text-xs">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div class="truncate">
                                <p class="text-xs font-black text-stone-900 dark:text-white truncate">{{ Auth::user()->name }}</p>
                                <p class="text-[10px] text-stone-500 font-bold uppercase">Perfil</p>
                            </div>
                        </div>
                        <i class="fa-solid fa-gear text-xs text-stone-400"></i>
                    </a>
                @else
                    <a href="/login" class="flex items-center justify-center gap-2 py-3 rounded-full bg-[#d4ff00] hover:bg-lime-400 text-black font-black text-xs uppercase tracking-wider transition-all shadow-xs">
                        <i class="fa-solid fa-right-to-bracket text-xs"></i>
                        <span>Iniciar Sessió</span>
                    </a>
                @endif
            </div>
        </div>

        <div id="container" class="min-h-screen">

            <div class="w-full lg:w-11/12 xl:w-10/12 max-w-[1400px] p-4 lg:p-6 mx-auto my-0">
                <div id="pwaNav" class="px-4 pb-2 flex justify-between items-center w-full border-b border-stone-200 dark:border-neutral-800 hidden lg:hidden">
                    <div id="pwaNavBack"><i class="fa-solid fa-backward-step cursor-pointer text-stone-600 dark:text-neutral-400" onClick="goBack()" onKeyPress="goBack()" role="button" tabindex="0"></i></div>
                    <div id="pwaNavFordward" class="hidden"><i class="fa-solid fa-forward-step cursor-pointer text-stone-600 dark:text-neutral-400" onClick="goForward()" onKeyPress="goForward()" role="button" tabindex="0"></i></div>
                </div>
                @include('layout.nav')
                {{-- @include('layout.select-section') --}}
                @yield('content')
            </div>
            <footer class="w-full justify-center  sticky top-[100vh] bg-neutral-900">
                @include('layout.merchandising')
                <div class="flex w-full justify-center py-8 ">
                    <div class="w-3/4 flex">
                        <div class="w-1/3 text-white text-left"><span class="jok">JOK.cat</span><br>http://www.jok.cat<br>jok@jok.cat<br><a href="/privacitat" class="text-sm text-gray-400 hover:text-white underline">Privacitat</a></div>
                        <div class="w-1/3 text-white">&nbsp;</div>
                        <div class="w-1/3  text-white">&nbsp;</div>
                    </div>
                </div>
            </footer>
</div>
            @vite(['resources/js/app.js'])
        <script src="{{ asset('pwa/pwa-install.js') }}"></script>

        {{-- Cookie Banner - Only show on desktop if no consent saved --}}
        <div id="cookie-banner" class="fixed bottom-0 left-0 right-0 bg-stone-900/95 backdrop-blur-md text-white p-4 z-50 border-t border-stone-800 shadow-2xl font-display" style="display: none;">
            <div class="max-w-4xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="text-xs md:text-sm text-center md:text-left text-stone-300">
                    <p>Utilitzem cookies per millorar la teva experiència. 
                        <a href="/privacitat" class="text-[#d4ff00] underline hover:text-white transition-colors font-bold">Més informació</a>
                    </p>
                </div>
                <div class="flex gap-3">
                    <button onclick="acceptCookies()" class="px-5 py-2 bg-[#d4ff00] hover:bg-[#c6f800] text-black rounded-full text-xs font-black uppercase tracking-wider transition-all shadow-sm">
                        Acceptar
                    </button>
                    <button onclick="rejectCookies()" class="px-4 py-2 border border-stone-700 hover:bg-stone-800 rounded-full text-xs font-extrabold text-stone-300 transition-colors">
                        Rebutjar
                    </button>
                </div>
            </div>
        </div>

        <script>
            // Cookie Banner functions
            function acceptCookies() {
                localStorage.setItem('cookie_consent', 'accepted');
                var banner = document.getElementById('cookie-banner');
                if (banner) banner.style.display = 'none';
            }

            function rejectCookies() {
                localStorage.setItem('cookie_consent', 'rejected');
                var banner = document.getElementById('cookie-banner');
                if (banner) banner.style.display = 'none';
            }

            // Show banner ONLY if no consent exists and screen width >= 768px
            if (!localStorage.getItem('cookie_consent') && window.innerWidth >= 768) {
                var banner = document.getElementById('cookie-banner');
                if (banner) banner.style.display = 'block';
            }

            const canGoBack = () => window.history.length > 1;

            if (canGoBack()) {
                document.getElementById("pwaNav").classList.remove("hidden");
            }
            if (canGoBack()) {
                document.getElementById("pwaNavBack").classList.remove("hidden");
            }

            const goBack = () => {
                if (canGoBack()) window.history.back();
            };
            const predictLocks = {}; // evita crides duplicades per id
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) { // si es veu
                        const id_match = entry.target.dataset.idMatch;
                        predict(id_match);
                        observer.unobserve(entry.target); // només 1 vegada
                    }
                });
            }, { threshold: 0.3 }); // es dispara quan el 30% del div és visible
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[id^="predict_"]').forEach(el => {
                    const id_match = el.id.replace('predict_', '');
                    el.dataset.idMatch = id_match;
                    observer.observe(el);
                });
            });
        </script>
</html>
