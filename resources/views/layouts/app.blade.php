<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PresensiEdu') — Clash of Champions</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Orbitron:wght@500;700;900&display=swap" rel="stylesheet">

    {{-- Vite assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Livewire styles & scripts --}}
    @livewireStyles
    @stack('styles')
</head>
<body class="h-full min-h-screen flex flex-col">
    {{-- Top header (clash of champions banner) --}}
    @include('partials.header')

    {{-- Main layout with sidebar (admin) or full-width (siswa) --}}
    <div class="flex-1 flex">
        @yield('layout-body')
    </div>

    {{-- Footer --}}
    @include('partials.footer')

    @livewireScripts
    @stack('scripts')
</body>
</html>
