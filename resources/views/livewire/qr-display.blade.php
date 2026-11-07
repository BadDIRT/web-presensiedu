@php
    $student = \App\Models\Student::find($this->studentId);
    $nama = $student?->name ?? '';
    $id = $student?->id_siswa ?? '';
    $kelas = $student?->kelas ?? '';
@endphp

<div class="text-center">
    {{-- Header banner --}}
    <div class="mb-5">
        <span class="title-banner">QR CODE PRIBADI</span>
        <p class="text-sm text-slate-400 mt-3 max-w-md mx-auto">
            Tunjukkan QR ini ke admin untuk di-scan.
            <span class="text-[#22e07b] font-semibold">QR ini permanen</span> —
            simpan screenshot-nya untuk digunakan kapan saja.
        </p>
    </div>

    {{-- Info siswa --}}
    <div class="coc-panel-flat p-4 mb-5 inline-flex flex-col items-center">
        <div class="text-[11px] uppercase tracking-widest text-slate-400">Peserta</div>
        <div class="font-display font-bold text-lg text-[#38bdf8] text-glow-blue">{{ $nama }}</div>
        <div class="flex items-center gap-2 mt-1 text-xs">
            <span class="font-display tracking-widest text-[#ffcc33]">{{ $id }}</span>
            @if ($kelas)
                <span class="text-slate-400">· {{ $kelas }}</span>
            @endif
        </div>
    </div>

    {{-- QR Code (permanen) --}}
    <div class="flex justify-center mb-4">
        <div class="qr-frame">
            <div class="flex flex-col items-center">
                @if (!empty($qrToken))
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(220)->color(10, 15, 36)->generate($qrToken) !!}
                @else
                    <div class="w-[220px] h-[220px] flex items-center justify-center text-slate-400">Memuat...</div>
                @endif
                <div class="mt-2 text-[10px] uppercase tracking-widest text-slate-400">PRESENSIEDU</div>
            </div>
        </div>
    </div>

    {{-- Badge permanen --}}
    <div class="flex items-center justify-center gap-3 mb-4">
        <div class="coc-panel-flat px-4 py-2 flex items-center gap-3">
            <svg class="w-4 h-4 text-[#22e07b]" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            <span class="text-xs uppercase tracking-widest text-slate-400">Status</span>
            <span class="font-display font-bold text-sm text-[#22e07b]">PERMANEN</span>
        </div>
    </div>

    {{-- Tips --}}
    <div class="coc-panel-flat p-3 max-w-md mx-auto">
        <div class="flex items-start gap-2 text-left">
            <svg class="w-5 h-5 text-[#38bdf8] shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="text-xs text-slate-300 leading-relaxed">
                QR code ini adalah QR pribadi Anda dan tidak akan berubah. Boleh di-screenshot
                untuk cadangan. Pastikan layar cukup terang saat di-scan admin.
            </div>
        </div>
    </div>
</div>
