@php
    use App\Models\Absensi;
@endphp

<div x-data="{}"
    @attendance-added.window="$wire.refreshList($event.detail.userId ?? 0, $event.detail.status ?? '')"
    wire:poll.30s="refreshList" class="flex flex-col gap-3">
    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-2">
        <div class="flex items-center gap-3">
            <span class="title-banner text-sm">DAFTAR ABSENSI HARI INI</span>
            <span class="text-xs text-slate-400">Update terakhir: <span
                    class="text-[#22e07b] font-semibold">{{ $lastUpdate }}</span></span>
        </div>
        <div class="flex items-center gap-2">
            <div class="coc-panel-flat px-3 py-1.5 flex items-center gap-2">
                <span class="live-dot"></span>
                <span class="text-[10px] uppercase tracking-widest text-slate-300 font-semibold">LIVE</span>
            </div>
            <div class="coc-panel-flat px-3 py-1.5">
                <span class="text-[10px] uppercase tracking-widest text-slate-500">Total</span>
                <span class="text-sm font-bold text-[#ffcc33] ml-1">{{ $totalToday }}</span>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="coc-panel overflow-hidden">
        {{-- Desktop table --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr
                        class="text-[10px] uppercase tracking-widest text-slate-400 border-b border-[var(--color-coc-border)]">
                        <th class="text-left px-4 py-3 font-semibold">#</th>
                        <th class="text-left px-4 py-3 font-semibold">ID Siswa</th>
                        <th class="text-left px-4 py-3 font-semibold">Nama</th>
                        <th class="text-left px-4 py-3 font-semibold">Kelas</th>
                        <th class="text-left px-4 py-3 font-semibold">Jam Scan</th>
                        <th class="text-left px-4 py-3 font-semibold">Status</th>
                        <th class="text-left px-4 py-3 font-semibold">Scan Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($absensis as $i => $absen)
                        <tr
                            class="border-b border-[rgba(42,55,102,0.4)] hover:bg-white/[0.03] {{ $freshId === $absen->id ? 'row-fresh' : '' }}">
                            <td class="px-4 py-2.5 text-slate-500 text-xs font-mono">
                                {{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-4 py-2.5 font-display tracking-wider text-[#38bdf8]">
                                {{ $absen->student?->id_siswa ?? '-' }}</td>
                            <td class="px-4 py-2.5 font-semibold text-white">
                                {{ $absen->student?->name ?? '(siswa dihapus)' }}</td>
                            <td class="px-4 py-2.5 text-slate-300">{{ $absen->student?->kelas ?? '-' }}</td>
                            <td class="px-4 py-2.5 font-mono text-[#ffcc33]">
                                {{ $absen->jam_masuk?->format('H:i:s') ?? '-' }}</td>
                            <td class="px-4 py-2.5">
                                <span class="badge {{ $absen->badgeClass() }}">{{ $absen->labelStatus() }}</span>
                            </td>
                            <td class="px-4 py-2.5 text-slate-400 text-xs">{{ $absen->scanner?->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-slate-500">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-10 h-10 opacity-30" fill="none" stroke="currentColor"
                                        stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div class="text-sm">Belum ada siswa yang absen hari ini.</div>
                                    <div class="text-xs text-slate-600">Mulai scan QR siswa untuk melihat data di sini.
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile cards --}}
        <div class="sm:hidden divide-y divide-[rgba(42,55,102,0.4)]">
            @forelse ($absensis as $i => $absen)
                <div class="p-4 {{ $freshId === $absen->id ? 'row-fresh' : '' }}">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="font-semibold text-white">{{ $absen->student?->name ?? '(siswa dihapus)' }}
                            </div>
                            <div class="text-xs text-slate-400">
                                <span
                                    class="text-[#38bdf8] font-display tracking-wider">{{ $absen->student?->id_siswa ?? '-' }}</span>
                                @if ($absen->student?->kelas)
                                    · {{ $absen->student->kelas }}
                                @endif
                            </div>
                        </div>
                        <span class="badge {{ $absen->badgeClass() }}">{{ $absen->labelStatus() }}</span>
                    </div>
                    <div class="flex items-center justify-between mt-2 text-xs">
                        <span class="text-slate-500">Jam scan:</span>
                        <span class="font-mono text-[#ffcc33]">{{ $absen->jam_masuk?->format('H:i:s') ?? '—' }}</span>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-500">
                    <div class="text-sm">Belum ada yang absen hari ini.</div>
                </div>
            @endforelse
        </div>
    </div>
</div>
