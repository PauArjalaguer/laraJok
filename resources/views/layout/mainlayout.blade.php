<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite('resources/css/app.css')
    <title>@yield('title')</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300..700&display=swap" rel="stylesheet">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
      @cookieconsentscripts
</head>

<body class="antialiased">
    <div id="container" class="min-h-screen">
        <div class="w-full lg:w-3/4 p-2 lg:p-0 mx-auto my-0 ">
            @include('layout.nav')
            @include('layout.select-section')
            @yield('content')
        </div>
        <footer class="w-full justify-center  sticky top-[100vh] bg-slate-900">
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
    @cookieconsentview
</body>

</html>
