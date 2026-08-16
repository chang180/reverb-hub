<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-svh bg-slate-950 text-slate-100 antialiased">
        {{ $slot }}
        @fluxScripts
    </body>
</html>
