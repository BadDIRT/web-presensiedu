@extends('layouts.app')

@section('title', 'Riwayat Absen')

@section('layout-body')
    @include('partials.sidebar-siswa')

    <main class="flex-1 p-4 sm:p-6 lg:p-8">
        {{-- Hero --}}
        <div class="coc-hero p-5 mb-6 relative overflow-hidden">
            <div class="absolute inset-0 -z-10 opacity-30" style="background: radial-gradient(circle at 50% 0%, rgba(56,189,248,0.4), transparent 60%);"></div>
            <div>
                <span class="title-banner text-sm">RIWAYAT PETUALANGAN</span>
                <h1 class="font-display font-black text-2xl sm:text-3xl mt-3 text-glow-blue">RIWAYAT ABSENSI SAYA</h1>
                <p class="text-sm text-slate-300 mt-1">Daftar lengkap kehadiranmu di arena PresensiEdu.</p>
            </div>
        </div>

        {{-- List --}}
        <div class="coc-panel overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-[10px] uppercase tracking-widest text-slate-400 border-b border-[var(--color-coc-border)]">
                            <th class="text-left px-4 py-3 font-semibold">#</th>
                            <th class="text-left px-4 py-3 font-semibold">Tanggal</th>
                            <th class="text-left px-4 py-3 font-semibold">Hari</th>
                            <th class="text-left px-4 py-3 font-semibold">Jam Masuk</th>
                            <th class="text-left px-4 py-3 font-semibold">Status</th>
                            <th class="text-left px-4 py-3 font-semibold">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($absensis as $i => $absen)
                            <tr class="border-b border-[rgba(42,55,102,0.4)] hover:bg-white/[0.03]">
                                <td class="px-4 py-2.5 text-slate-500 text-xs font-mono">{{ ($absensis->currentPage() - 1) * $absensis->perPage() + $i + 1 }}</td>
                                <td class="px-4 py-2.5 font-semibold text-white">{{ $absen->tanggal->format('d M Y') }}</td>
                                <td class="px-4 py-2.5 text-slate-300">{{ $absen->tanggal->translatedFormat('l') }}</td>
                                <td class="px-4 py-2.5 font-mono text-[#ffcc33]">{{ $absen->jam_masuk?->format('H:i:s') ?? '-' }}</td>
                                <td class="px-4 py-2.5">
                                    <span class="badge {{ $absen->badgeClass() }}">{{ $absen->labelStatus() }}</span>
                                </td>
                                <td class="px-4 py-2.5 text-slate-400 text-xs">{{ $absen->keterangan ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-slate-500">
                                    Belum ada riwayat absensi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($absensis->hasPages())
                <div class="px-4 py-3 border-t border-[var(--color-coc-border)]">
                    {{ $absensis->links() }}
                </div>
            @endif
        </div>

        @include('partials.alerts')
    </main>
@endsection
