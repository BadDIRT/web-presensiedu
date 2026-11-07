@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('layout-body')
    @include('partials.sidebar-admin')

    <main class="flex-1 p-4 sm:p-6 lg:p-8">
        {{-- Hero banner --}}
        <div class="coc-hero p-5 sm:p-7 mb-6 relative overflow-hidden">
            <div class="absolute inset-0 -z-10 opacity-30"
                style="background: radial-gradient(circle at 80% 0%, rgba(255,71,87,0.4), transparent 60%);"></div>
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <span class="title-banner text-sm">ARENA ADMIN</span>
                    <h1 class="font-display font-black text-2xl sm:text-3xl mt-3 text-glow-gold">DASHBOARD KOMANDAN</h1>
                    <p class="text-sm text-slate-300 mt-1 max-w-lg">Pantau pertempuran absensi harian. Statistik diperbarui
                        otomatis saat scan baru masuk.</p>
                </div>
                {{-- Menjadi: --}}
                @include('partials.live-clock')
            </div>
        </div>

        {{-- Live stats --}}
        <div class="mb-6">
            <livewire:stat-cards />
        </div>

        {{-- Quick actions --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <a href="{{ route('admin.scan') }}"
                class="coc-panel coc-corner p-5 group hover:border-[#ffcc33] transition-all">
                <div class="flex items-center gap-4">
                    <div
                        class="w-14 h-14 rounded-xl bg-gradient-to-br from-[#ffcc33] to-[#ff9d2e] flex items-center justify-center shadow-[0_0_24px_rgba(255,204,51,0.4)]">
                        <svg class="w-7 h-7 text-[#1a1306]" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 4h4m6 0h4M4 4v4m16-4v4M4 16v4m0-4h4m10 4v-4m0 4h-4M4 20h4m6 0h4"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="font-display font-bold text-lg text-white group-hover:text-[#ffcc33]">SCAN QR SISWA
                        </div>
                        <div class="text-xs text-slate-400">Buka kamera &amp; mulai memindai absensi siswa</div>
                    </div>
                    <svg class="w-5 h-5 text-slate-500 group-hover:text-[#ffcc33] transition-colors" fill="none"
                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </div>
            </a>

            <a href="{{ route('admin.rekap') }}"
                class="coc-panel coc-corner p-5 group hover:border-[#38bdf8] transition-all">
                <div class="flex items-center gap-4">
                    <div
                        class="w-14 h-14 rounded-xl bg-gradient-to-br from-[#38bdf8] to-[#2563eb] flex items-center justify-center shadow-[0_0_24px_rgba(56,189,248,0.4)]">
                        <svg class="w-7 h-7 text-[#04101f]" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 17v-2m3 2v-4m3 4v-6M2 5l8-3 8 3M2 5v14l8 3 8-3V5M2 5h16"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="font-display font-bold text-lg text-white group-hover:text-[#38bdf8]">REKAP HARIAN</div>
                        <div class="text-xs text-slate-400">Lihat &amp; ekspor rekap absensi per tanggal</div>
                    </div>
                    <svg class="w-5 h-5 text-slate-500 group-hover:text-[#38bdf8] transition-colors" fill="none"
                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </div>
            </a>
        </div>

        {{-- Live attendance list --}}
        <livewire:attendance-list />

        {{-- 🆕 Salin Info Absensi untuk grup orang tua --}}
        <div class="mt-6">
            <livewire:attendance-info />
        </div>

        @include('partials.alerts')
    </main>
@endsection
