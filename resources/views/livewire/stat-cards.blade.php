<div
    x-data="{}"
    @attendance-added.window="$wire.refreshStats()"
    wire:poll.60s="refreshStats"
    class="grid grid-cols-1 sm:grid-cols-3 gap-3"
>
    {{-- Total Siswa --}}
    <div class="stat-card">
        <div class="absolute -right-5 -top-5 w-16 h-16 rounded-full" style="background: rgba(56,189,248,0.3); filter: blur(20px);"></div>
        <div class="relative">
            <div class="text-[10px] uppercase tracking-widest text-slate-400">Total Siswa Aktif</div>
            <div class="font-display font-black text-3xl text-[#38bdf8] text-glow-blue mt-1">{{ $totalSiswa }}</div>
            <div class="text-[10px] text-slate-500 mt-0.5">Terdaftar & aktif</div>
        </div>
    </div>

    {{-- Hadir --}}
    <div class="stat-card" wire:key="stat-hadir">
        <div class="absolute -right-5 -top-5 w-16 h-16 rounded-full" style="background: rgba(34,224,123,0.3); filter: blur(20px);"></div>
        <div class="relative">
            <div class="text-[10px] uppercase tracking-widest text-slate-400">✅ Hadir</div>
            <div class="font-display font-black text-3xl text-[#22e07b] mt-1">{{ $hadir }}</div>
            <div class="text-[10px] text-slate-500 mt-0.5">
                @if ($totalSiswa > 0)
                    {{ round(($hadir / $totalSiswa) * 100, 1) }}% dari total
                @else
                    -
                @endif
            </div>
        </div>
    </div>

    {{-- Tidak Hadir / Izin --}}
    <div class="stat-card" wire:key="stat-izin">
        <div class="absolute -right-5 -top-5 w-16 h-16 rounded-full" style="background: rgba(255,71,87,0.3); filter: blur(20px);"></div>
        <div class="relative">
            <div class="text-[10px] uppercase tracking-widest text-slate-400">❌ Tidak Hadir/Izin</div>
            <div class="font-display font-black text-3xl text-[#ff4757] text-glow-red mt-1">{{ $izin }}</div>
            <div class="text-[10px] text-slate-500 mt-0.5">
                @if ($totalSiswa > 0)
                    {{ round(($izin / $totalSiswa) * 100, 1) }}% dari total
                @else
                    -
                @endif
            </div>
        </div>
    </div>
</div>
