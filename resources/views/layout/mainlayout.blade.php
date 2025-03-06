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
    <script src="https://kit.fontawesome.com/0b5c081e1f.js" crossorigin="anonymous"></script>
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

    </script>
</head>

<body class="antialiased">

    <div class="relative">

        <!-- Sidebar -->
        <div id="sidebar" class="fixed left-0 top-0 w-[55%] h-full bg-gray-800 text-white z-50 -translate-x-full transition-transform duration-1000 ease-in-out">

            <div class="text-gray-100 text-xl">
                <div class="p-2 mt-1 flex items-center justify-between ">
                    <h1 class="text-[15px]  ml-3 text-xl text-white font-bold  font-['Comfortaa']">Jok.cat</h1>
                    <i class="fa-solid fa-circle-xmark h-6 w-6 cursor-pointer lg:hidden hover:text-gray-300" onClick="toggleMenu()" onKeyPress="toggleMenu()" role="button" tabindex="0"></i>
                </div>
                <hr class="my-2 text-gray-600">

                <div>
                    <div class="p-2.5 m-2 flex items-center rounded-md px-4 duration-300 cursor-pointer  hover:bg-gray-500">
                        <i class="fa-solid fa-house-laptop"></i>
                        <a href="/"><span class="text-[15px] ml-4 text-white">Inici</span></a>
                    </div>
                    <div class="p-2.5 m-2 flex items-center rounded-md px-4 duration-300 cursor-pointer  hover:bg-gray-500">
                        <i class="fa-regular fa-newspaper"></i>
                        <a href="/noticies"><span class="text-[15px] ml-4 text-white">Notícies</span></a>
                    </div>
                    <div class="p-2.5 m-2 flex items-center rounded-md px-4 duration-300 cursor-pointer  hover:bg-gray-500">
                        <i class="fa-solid fa-building"></i>
                        <a href="/pavellons"><span class="text-[15px] ml-4 text-white">Pavellons</span></a>
                    </div>
                    <div class="p-2.5 m-2 flex items-center rounded-md 
        px-4 duration-300 cursor-pointer ">
                        <i class="fa-brands fa-searchengin"></i>
                        <input class="text-[15px] ml-4 w-full bg-transparent focus:outline-none" placeholder="Buscar" onKeyUp="search(this.value)" />
                    </div>
                    <hr class="my-4 text-gray-600">
                    <div class="p-2.5 m-2 flex items-center rounded-md px-4 duration-300 cursor-pointer  hover:bg-gray-500">
                        <i class="fa-regular fa-calendar-days text-white"></i>
                        <a href="/agenda"><span class="text-[15px] ml-4 text-white">Agenda</span></a>
                    </div>
                    <div class="p-2.5 m-2 flex items-center rounded-md px-4 duration-300 cursor-pointer  hover:bg-gray-500">
                        <i class="fa-sharp-duotone fa-solid fa-shirt"></i>
                        <a href="/merchandising"><span class="text-[15px] ml-4 text-white">Merchandising</span></a>
                    </div>

                    <div class="p-2.5 m-2 flex items-center rounded-md px-4 duration-300 cursor-pointer  hover:bg-gray-500" id="install-btn" style="display: none;">
                        <i class="fa-solid fa-mobile-screen"></i>
                        <button class="text-[15px] ml-4 text-white">Instal·lar App a Android</button>
                    </div>
                    @if (Auth::check())
                    <div class="p-2.5 m-2 flex items-center rounded-md px-4 duration-300 cursor-pointer  hover:bg-gray-500">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <a href='/logout'><span class="text-[15px] ml-4 text-white">Logout</span></a>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <div id="container" class="min-h-screen">

        <div class="w-full lg:w-3/4 p-2 lg:p-0 mx-auto my-0 ">
            <div id="pwaNav" class="px-2 pb-2 flex justify-between items-center w-full border-b border-gray-200 hidden lg:hidden">
                <div id="pwaNavBack"><i class="fa-solid fa-backward-step" onClick="goBack()" onKeyPress="goBack()" role="button" tabindex="0"></i></div>
                <div id="pwaNavFordward" class="hidden"><i class="fa-solid fa-forward-step" onClick="goForward()" onKeyPress="goForward()" role="button" tabindex="0"></i></div>
            </div>
            @include('layout.nav')
            @include('layout.select-section')
            @yield('content')
        </div>
        <footer class="w-full justify-center  sticky top-[100vh] bg-neutral-900">
            @include('layout.merchandising')
            <div class="flex w-full justify-center py-8 ">
                <div class="w-3/4 flex">
                    <div class="w-1/3 text-white text-left"><span class="jok">JOK.cat</span><br>http://www.jok.cat<br>jok@jok.cat</div>
                    <div class="w-1/3 text-white">&nbsp;</div>
                    <div class="w-1/3  text-white">&nbsp;</div>
                </div>
            </div>
        </footer>
    </div>
    @vite(['resources/js/app.js'])
    <script src="{{ asset('pwa/pwa-install.js') }}"></script>
</body>
<script>
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

</script>
</html>
