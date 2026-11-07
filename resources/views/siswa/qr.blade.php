@extends('layouts.app')

@section('title', 'QR Code Saya')

@section('layout-body')
    @include('partials.sidebar-siswa')

    <main class="flex-1 p-4 sm:p-6 lg:p-8">
        {{-- Hero --}}
        <div class="coc-hero p-5 mb-6 relative overflow-hidden">
            <div class="absolute inset-0 -z-10 opacity-30" style="background: radial-gradient(circle at 50% 0%, rgba(255,204,51,0.4), transparent 60%);"></div>
            <div class="text-center">
                <span class="title-banner text-sm">KARTU PERTANDINGAN</span>
                <h1 class="font-display font-black text-2xl sm:text-3xl mt-3 text-glow-gold">QR CODE PRIBADI</h1>
                <p class="text-sm text-slate-300 mt-1 max-w-lg mx-auto">Tunjukkan QR ini ke admin untuk di-scan saat absensi.</p>
            </div>
        </div>

        {{-- QR component --}}
        <div class="coc-panel coc-corner p-6 sm:p-8 max-w-lg mx-auto">
            <livewire:qr-display :student-id="$student->id" />
        </div>

        {{-- Info cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-6 max-w-lg mx-auto">
            <div class="coc-panel-flat p-3 text-center">
                <svg class="w-6 h-6 mx-auto text-[#22e07b] mb-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-[10px] uppercase tracking-widest text-slate-400">Aman</div>
                <div class="text-xs text-slate-300 mt-0.5">Token terenkripsi</div>
            </div>
            <div class="coc-panel-flat p-3 text-center">
                <svg class="w-6 h-6 mx-auto text-[#ffcc33] mb-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-[10px] uppercase tracking-widest text-slate-400">Auto Refresh</div>
                <div class="text-xs text-slate-300 mt-0.5">Setiap 30 detik</div>
            </div>
            <div class="coc-panel-flat p-3 text-center">
                <svg class="w-6 h-6 mx-auto text-[#38bdf8] mb-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                <div class="text-[10px] uppercase tracking-widest text-slate-400">Cepat</div>
                <div class="text-xs text-slate-300 mt-0.5">Real-time scan</div>
            </div>
        </div>

        @include('partials.alerts')
    </main>
@endsection
