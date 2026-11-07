{{-- Layout khusus halaman login (tanpa sidebar) --}}
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') — PresensiEdu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Orbitron:wght@500;700;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full min-h-screen flex items-center justify-center p-4">
    {{-- Background animated particles via CSS --}}
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-32 -left-24 w-96 h-96 rounded-full opacity-30 blur-3xl"
             style="background: radial-gradient(circle, #38bdf8 0%, transparent 70%); animation: qr-shimmer 6s ease-in-out infinite;"></div>
        <div class="absolute -bottom-32 -right-24 w-96 h-96 rounded-full opacity-30 blur-3xl"
             style="background: radial-gradient(circle, #ff4757 0%, transparent 70%); animation: qr-shimmer 8s ease-in-out infinite reverse;"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full opacity-10 blur-3xl"
             style="background: radial-gradient(circle, #ffcc33 0%, transparent 70%);"></div>
    </div>

    @yield('content')
</body>
</html>
