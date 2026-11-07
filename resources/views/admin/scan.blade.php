@extends('layouts.app')

@section('title', 'Scan QR')

@section('layout-body')
    @include('partials.sidebar-admin')

    <main class="flex-1 p-4 sm:p-6 lg:p-8">
        {{-- Hero --}}
        <div class="coc-hero p-5 mb-6 relative overflow-hidden">
            <div class="absolute inset-0 -z-10 opacity-25" style="background: radial-gradient(circle at 50% 0%, rgba(255,204,51,0.5), transparent 60%);"></div>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <span class="title-banner text-sm">ARENA SCAN</span>
                    <h1 class="font-display font-black text-2xl sm:text-3xl mt-3 text-glow-gold">SCAN QR CODE SISWA</h1>
                    <p class="text-sm text-slate-300 mt-1">Arahkan kamera ke QR siswa. Data absensi tersimpan <span class="text-[#22e07b] font-semibold">real-time</span>.</p>
                </div>
                <div class="coc-panel-flat px-4 py-2">
                    <span class="text-[10px] uppercase tracking-widest text-slate-400">Mode</span>
                    <div class="font-display font-bold text-[#ffcc33]">JAM FLEKSIBEL</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">Scan = Hadir</div>
                </div>
            </div>
        </div>

        {{-- Scanner --}}
        <livewire:qr-scanner />

        {{-- Live attendance (same list) --}}
        <div class="mt-6">
            <livewire:attendance-list />
        </div>

        @include('partials.alerts')
    </main>
@endsection
