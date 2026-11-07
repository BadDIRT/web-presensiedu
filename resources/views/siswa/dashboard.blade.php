@extends('layouts.app')

@section('title', 'Dashboard Siswa')

@section('layout-body')
    @include('partials.sidebar-siswa')

    <main class="flex-1 p-4 sm:p-6 lg:p-8">
        {{-- Hero --}}
        <div class="coc-hero p-5 sm:p-7 mb-6 relative overflow-hidden">
            <div class="absolute inset-0 -z-10 opacity-30" style="background: radial-gradient(circle at 0% 100%, rgba(56,189,248,0.4), transparent 60%);"></div>
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <span class="title-banner text-sm">ARENA PESERTA</span>
                    <h1 class="font-display font-black text-2xl sm:text-3xl mt-3 text-glow-blue">SELAMAT DATANG</h1>
                    <p class="text-lg text-white font-semibold mt-1">{{ $student->name }}</p>
                    <div class="flex items-center gap-3 text-xs text-slate-300 mt-1">
                        <span class="font-display tracking-widest text-[#ffcc33]">{{ $student->id_siswa }}</span>
                        @if ($student->kelas)
                            <span>· Kelas {{ $student->kelas }}</span>
                        @endif
                    </div>
                </div>
                @include('partials.live-clock')
            </div>
        </div>

        {{-- Status absen hari ini --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
            <div class="coc-panel coc-corner p-5 lg:col-span-2">
                @if ($absenHariIni)
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-[10px] uppercase tracking-widest text-slate-400 mb-1">Status Absen Hari Ini</div>
                            <div class="flex items-center gap-3">
                                <span class="badge {{ $absenHariIni->badgeClass() }} !text-base !py-1.5 !px-4">{{ $absenHariIni->labelStatus() }}</span>
                                <div class="text-sm text-slate-300">
                                    Jam masuk: <span class="font-mono font-bold text-[#ffcc33]">{{ $absenHariIni->jam_masuk?->format('H:i:s') }}</span>
                                </div>
                            </div>
                            <div class="text-xs text-slate-400 mt-2">Sudah tercatat hadir. Tetap semangat untuk hari esok!</div>
                        </div>
                        <svg class="w-12 h-12 text-[#22e07b] shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                @else
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-[10px] uppercase tracking-widest text-slate-400 mb-1">Status Absen Hari Ini</div>
                            <div class="font-display font-bold text-2xl text-[#ff4757]">BELUM ABSEN</div>
                            <div class="text-sm text-slate-300 mt-1">Tunjukkan QR code kamu ke admin untuk di-scan.</div>
                            <a href="{{ route('siswa.qr') }}" class="btn-coc btn-gold mt-3 !py-2 text-xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M3 9h2m-2 6h2M7 4h2M7 20h2m-4-7v3a2 2 0 002 2h10a2 2 0 002-2V8a2 2 0 00-2-2H7a2 2 0 00-2 2v3z"></path>
                                </svg>
                                Tampilkan QR Saya
                            </a>
                        </div>
                        <svg class="w-12 h-12 text-[#ff4757] shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                @endif
            </div>

            <div class="coc-panel p-5 flex items-center gap-4">
                <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-[#ffcc33] to-[#ff9d2e] flex items-center justify-center shadow-[0_0_24px_rgba(255,204,51,0.4)]">
                    <span class="font-display font-black text-2xl text-[#1a1306]">{{ $statistik['hadir'] }}</span>
                </div>
                <div>
                    <div class="text-[10px] uppercase tracking-widest text-slate-400">Total Hadir</div>
                    <div class="font-display font-bold text-xl text-white">Periode ini</div>
                    <div class="text-xs text-slate-400 mt-0.5">Jam fleksibel</div>
                </div>
            </div>
        </div>

        {{-- Statistik pribadi (2 status: hadir & izin) --}}
        <div class="grid grid-cols-2 sm:grid-cols-2 gap-3 mb-6">
            <div class="stat-card"><div class="text-[10px] uppercase tracking-widest text-slate-400">✅ Hadir</div><div class="font-display font-bold text-3xl text-[#22e07b] mt-1">{{ $statistik['hadir'] }}</div></div>
            <div class="stat-card"><div class="text-[10px] uppercase tracking-widest text-slate-400">❌ Tidak Hadir/Izin</div><div class="font-display font-bold text-3xl text-[#ff4757] mt-1">{{ $statistik['izin'] }}</div></div>
        </div>

        {{-- Riwayat terbaru --}}
        <div class="coc-panel p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="title-banner text-sm">RIWAYAT TERBARU</span>
                <a href="{{ route('siswa.riwayat') }}" class="text-xs text-[#ffcc33] hover:underline">Lihat semua →</a>
            </div>

            @if ($riwayat->isEmpty())
                <div class="text-center py-8 text-slate-500 text-sm">
                    Belum ada riwayat absensi.
                </div>
            @else
                <div class="divide-y divide-[rgba(42,55,102,0.4)]">
                    @foreach ($riwayat as $absen)
                        <div class="py-3 flex items-center justify-between">
                            <div>
                                <div class="text-sm font-semibold text-white">{{ $absen->tanggal->format('d M Y') }}</div>
                                <div class="text-xs text-slate-400">{{ $absen->tanggal->translatedFormat('l') }}</div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="text-right">
                                    <div class="text-xs text-slate-400">Jam masuk</div>
                                    <div class="font-mono text-[#ffcc33]">{{ $absen->jam_masuk?->format('H:i:s') ?? '-' }}</div>
                                </div>
                                <span class="badge {{ $absen->badgeClass() }}">{{ $absen->labelStatus() }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        @include('partials.alerts')
    </main>
@endsection
