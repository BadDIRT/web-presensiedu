@extends('layouts.app')

@section('title', 'Rekap Harian')

@section('layout-body')
    @include('partials.sidebar-admin')

    <main class="flex-1 p-4 sm:p-6 lg:p-8">
        {{-- Hero --}}
        <div class="coc-hero p-5 mb-6 relative overflow-hidden">
            <div class="absolute inset-0 -z-10 opacity-25" style="background: radial-gradient(circle at 20% 0%, rgba(56,189,248,0.5), transparent 60%);"></div>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <span class="title-banner text-sm">REKAP HARIAN</span>
                    <h1 class="font-display font-black text-2xl sm:text-3xl mt-3 text-glow-blue">LAPORAN ABSENSI</h1>
                    <p class="text-sm text-slate-300 mt-1">Filter berdasarkan tanggal untuk melihat rekap absensi.</p>
                </div>
            </div>
        </div>

        {{-- Filter form --}}
        <form method="GET" action="{{ route('admin.rekap') }}" class="coc-panel p-4 mb-6">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-[10px] uppercase tracking-widest text-slate-400 font-semibold mb-1">Pilih Tanggal</label>
                    <input
                        type="date"
                        name="tanggal"
                        value="{{ $tanggal }}"
                        class="coc-input !py-2"
                    >
                </div>
                <button type="submit" class="btn-coc btn-gold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Tampilkan
                </button>
                <a href="{{ route('admin.rekap') }}" class="btn-coc btn-ghost">Hari ini</a>
            </div>
        </form>

        {{-- 🆕 Salin Info Absensi untuk grup orang tua (sync dengan tanggal filter) --}}
        <div class="mb-6">
            <livewire:attendance-info :tanggal="(string) $tanggal" :key="'info-' . $tanggal" />
        </div>

        {{-- Summary cards (hanya 2 status: hadir & izin) --}}
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-2 gap-3 mb-6">
            @php
                $summary = ['hadir' => 0, 'izin' => 0];
                foreach ($absensis as $a) {
                    if (isset($summary[$a->status])) $summary[$a->status]++;
                }
            @endphp
            <div class="stat-card"><div class="text-[10px] uppercase tracking-widest text-slate-400">✅ Hadir</div><div class="font-display font-bold text-3xl text-[#22e07b] mt-1">{{ $summary['hadir'] }}</div></div>
            <div class="stat-card"><div class="text-[10px] uppercase tracking-widest text-slate-400">❌ Tidak Hadir/Izin</div><div class="font-display font-bold text-3xl text-[#ff4757] mt-1">{{ $summary['izin'] }}</div></div>
        </div>

        {{-- Table --}}
        <div class="coc-panel overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-[10px] uppercase tracking-widest text-slate-400 border-b border-[var(--color-coc-border)]">
                            <th class="text-left px-4 py-3 font-semibold">#</th>
                            <th class="text-left px-4 py-3 font-semibold">NIS</th>
                            <th class="text-left px-4 py-3 font-semibold">Nama</th>
                            <th class="text-left px-4 py-3 font-semibold">Kelas</th>
                            <th class="text-left px-4 py-3 font-semibold">Jam Masuk</th>
                            <th class="text-left px-4 py-3 font-semibold">Status</th>
                            <th class="text-left px-4 py-3 font-semibold">Scan Oleh</th>
                            <th class="text-left px-4 py-3 font-semibold">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($absensis as $i => $absen)
                            <tr class="border-b border-[rgba(42,55,102,0.4)] hover:bg-white/[0.03]">
                                <td class="px-4 py-2.5 text-slate-500 text-xs font-mono">{{ str_pad((string)($i+1), 2, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-4 py-2.5 font-display tracking-wider text-[#38bdf8]">{{ $absen->student->id_siswa ?? '-' }}</td>
                                <td class="px-4 py-2.5 font-semibold text-white">{{ $absen->student->name ?? '-' }}</td>
                                <td class="px-4 py-2.5 text-slate-300">{{ $absen->student->kelas ?? '-' }}</td>
                                <td class="px-4 py-2.5 font-mono text-[#ffcc33]">{{ $absen->jam_masuk?->format('H:i:s') ?? '-' }}</td>
                                <td class="px-4 py-2.5">
                                    <span class="badge {{ $absen->badgeClass() }}">{{ $absen->labelStatus() }}</span>
                                </td>
                                <td class="px-4 py-2.5 text-slate-400 text-xs">{{ $absen->scanner?->name ?? '-' }}</td>
                                <td class="px-4 py-2.5 text-slate-400 text-xs">{{ $absen->keterangan ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-10 text-center text-slate-500">
                                    Tidak ada data absensi pada tanggal tersebut.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
@endsection
