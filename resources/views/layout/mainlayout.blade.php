<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="apple-mobile-web-app-status-bar" content="#ffffff">
    <meta name="apple-mobile-web-app-capable" content="yes">

    @vite('resources/css/app.css')
    <title>@yield('title')</title>
    <link rel="manifest" href="/manifest.json">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300..700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

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
        @keyframes vt-hero-in {
            from { opacity: 0; transform: scale(0.96); }
            to   { opacity: 1; transform: scale(1); }
        }
        </style>
</head>

<body class="antialiased">

    <div class="relative">
        <div id="sidebar" class="fixed left-0 top-0 w-[52%] h-full bg-neutral-800 text-white z-50 -translate-x-full transition-transform duration-1000 ease-in-out">
        @php
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            if(isset($userAgent) && $userAgent == 'iOSWebView'){
                echo "<div class='mt-20'>&nbsp;</div>";
            }
        @endphp
            <div class="text-gray-100 text-xl">
                <div class="p-4 mt-1 flex items-center justify-between ">
                    <i class="fa-solid fa-circle-xmark h-6 w-6 cursor-pointer lg:hidden hover:text-gray-300" onClick="toggleMenu()" onKeyPress="toggleMenu()" role="button" tabindex="0"></i>
                    <h1 class="text-[15px]  ml-3 text-xl text-white font-bold  font-['Comfortaa']">Jok.cat</h1>

                </div>
                <hr class="my-2 text-gray-600">

                <div>
                    <div class=" m-1 flex items-center rounded-md px-4 duration-300 cursor-pointer  hover:bg-gray-500">
                        <i class="fa-solid fa-house-laptop"></i>
                        <a href="/"><span class="text-[15px] ml-4 text-white">Inici</span></a>
                    </div>
                    <div class=" m-1 flex items-center rounded-md px-4 duration-300 cursor-pointer  hover:bg-gray-500">
                        <i class="fa-solid fa-person-skating"></i>
                        <a href="/competicions"><span class="text-[15px] ml-4 text-white">Competicions</span></a>
                    </div>
                    <div class=" m-1 flex items-center rounded-md px-4 duration-300 cursor-pointer  hover:bg-gray-500">
                        <i class="fa-regular fa-newspaper"></i>
                        <a href="/noticies"><span class="text-[15px] ml-4 text-white">Notícies</span></a>
                    </div>
                    <div class=" m-1 flex items-center rounded-md px-4 duration-300 cursor-pointer  hover:bg-gray-500">
                        <i class="fa-solid fa-location-pin"></i>
                        <a href="/pavellons"><span class="text-[15px] ml-4 text-white">Pavellons</span></a>
                    </div>
                    <div class=" m-1 flex items-center rounded-md px-4 duration-300 cursor-pointer ">
                        <i class="fa-brands fa-searchengin"></i>
                        <input class="text-[15px] ml-4 w-full bg-transparent focus:outline-none outline-no" placeholder="Buscar" onKeyUp="search(this.value)" />
                    </div>
                    <div id="sidebarSearchResults" class="p-2 text-sm text-center hidden"> resultats trobats</div>
                    <hr class="my-4 text-gray-600">
                    <div class=" m-1 flex items-center rounded-md px-4 duration-300 cursor-pointer  hover:bg-gray-500">
                        <i class="fa-solid fa-calendar-week text-white"></i>
                        <a href="/agenda"><span class="text-[15px] ml-4 text-white">Agenda</span></a>
                    </div>
                    @if (Auth::check())
                    <div class=" m-1 flex items-center rounded-md px-4 duration-300 cursor-pointer  hover:bg-gray-500">
                        <i class="fa-regular fa-calendar-days text-white"></i>
                        <a href="/calendari"><span class="text-[15px] ml-4 text-white">Calendari</span></a>
                    </div>
                    @endif

                    <div class=" m-1 flex items-center rounded-md px-4 duration-300 cursor-pointer  hover:bg-gray-500">
                        <i class="fa-sharp-duotone fa-solid fa-shirt"></i>
                        <a href="/merchandising"><span class="text-[15px] ml-4 text-white">Merchandising</span></a>
                    </div>

                    <div class=" m-1 flex items-center rounded-md px-4 duration-300 cursor-pointer  hover:bg-gray-500">
                        <i class="fa-solid fa-tags"></i>
                        <a href="/anuncis"><span class="text-[15px] ml-4 text-white">Segona Mà</span></a>
                    </div>

                    <div class=" m-1 flex items-center rounded-md px-4 duration-300 cursor-pointer  hover:bg-gray-500" id="install-btn" style="display: none;">
                        <i class="fa-solid fa-mobile-screen"></i>
                        <button class="text-[15px] ml-4 text-white">Instal·lar App</button>
                    </div>
                    @if (Auth::check())
                    <div class=" m-1 flex items-center rounded-md px-4 duration-300 cursor-pointer  hover:bg-gray-500">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <a href='/logout'><span class="text-[15px] ml-4 text-white">Logout</span></a>
                    </div>
                    @else
                    <div class=" m-1 flex items-center rounded-md px-4 duration-300 cursor-pointer  hover:bg-gray-500">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <a href='/login'><span class="text-[15px] ml-4 text-white">Login</span></a>
                        @endif

                    </div>
                </div>
            </div>
        </div>

        <div id="container" class="min-h-screen">

            <div class="w-full lg:w-3/4 p-2 lg:p-0 mx-auto my-0 ">
                <div id="pwaNav" class="px-4 pb-2 flex justify-between items-center w-full border-b border-gray-200 hidden lg:hidden">
                    <div id="pwaNavBack"><i class="fa-solid fa-backward-step" onClick="goBack()" onKeyPress="goBack()" role="button" tabindex="0"></i></div>
                    <div id="pwaNavFordward" class="hidden"><i class="fa-solid fa-forward-step" onClick="goForward()" onKeyPress="goForward()" role="button" tabindex="0"></i></div>
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

        {{-- Cookie Banner - Only show on desktop --}}
        <div id="cookie-banner" class="fixed bottom-0 left-0 right-0 bg-neutral-800 text-white p-4 z-50 hidden md:block">
            <div class="max-w-4xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="text-sm text-center md:text-left text-gray-300">
                    <p>Utilitzem cookies per millorar la teva experiència. 
                        <a href="/privacitat" class="text-white underline hover:text-gray-300">Més informació</a>
                    </p>
                </div>
                <div class="flex gap-3">
                    <button onclick="acceptCookies()" class="px-4 py-2 bg-neutral-600 hover:bg-neutral-500 rounded-lg text-sm font-semibold transition-colors">
                        Acceptar
                    </button>
                    <button onclick="rejectCookies()" class="px-4 py-2 border border-neutral-500 hover:bg-neutral-700 rounded-lg text-sm text-gray-300 transition-colors">
                        Rebutjar
                    </button>
                </div>
            </div>
        </div>

        <script>
            // Cookie Banner functions
            function acceptCookies() {
                console.log('accept clicked');
                localStorage.setItem('cookie_consent', 'accepted');
                document.getElementById('cookie-banner').style.display = 'none';
            }

            function rejectCookies() {
                console.log('reject clicked');
                localStorage.setItem('cookie_consent', 'rejected');
                document.getElementById('cookie-banner').style.display = 'none';
            }

            // Show banner if no consent
            if (!localStorage.getItem('cookie_consent')) {
                document.getElementById('cookie-banner').classList.remove('hidden');
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
