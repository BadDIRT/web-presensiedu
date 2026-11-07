<div x-data="{
    copied: false,
    async copyText(text) {
        try {
            await navigator.clipboard.writeText(text);
        } catch (e) {
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.style.position = 'fixed';
            ta.style.opacity = '0';
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
        }
        this.copied = true;
        window.cocToast && window.cocToast('Info absensi berhasil disalin! Silakan paste di grup orang tua.', 'success');
        setTimeout(() => this.copied = false, 2500);
    }
}" @copy-attendance-info.window="copyText($event.detail.text)"
    @scan-success.window="window.cocToast && window.cocToast($event.detail.message, 'success')"
    @scan-error.window="window.cocToast && window.cocToast($event.detail.message, 'error')" class="coc-panel p-5">
    <div class="flex items-center justify-between flex-wrap gap-3 mb-4">
        <div>
            <span class="title-banner text-sm">SALIN INFO ABSENSI</span>
            <p class="text-xs text-slate-400 mt-2 max-w-md">
                Salin ringkasan absensi (Hadir &amp; Tidak Hadir/Izin) untuk dibagikan ke grup
                WhatsApp orang tua siswa. Format sudah siap paste dengan emoji.
            </p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <input type="date" wire:model.live="tanggal" class="coc-input !py-2 !w-auto" aria-label="Pilih tanggal">
            <button type="button" @click="$wire.copyToClipboard()" class="btn-coc btn-gold whitespace-nowrap"
                :class="{ 'opacity-60': copied }">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h4a2 2 0 002-2M8 5a2 2 0 012-2h4a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                </svg>
                <span x-show="!copied">Salin Info</span>
                <span x-show="copied" class="text-[#22e07b]">✓ Tersalin!</span>
            </button>
            <button type="button"
                @click="if (confirm('Tandai semua siswa yang belum absen hari ini sebagai Tidak Hadir/Izin?')) $wire.markRemainingAsIzin()"
                class="btn-coc btn-red whitespace-nowrap" title="Tandai siswa yang belum scan sebagai Tidak Hadir/Izin">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Tandai Sisa Izin
            </button>
        </div>
    </div>

    <div class="coc-panel-flat p-4">
        <div class="flex items-center justify-between mb-2">
            <div class="text-[10px] uppercase tracking-widest text-slate-500">
                📋 Preview Info (siap paste ke grup):
            </div>
            <div class="text-[10px] text-slate-500 font-mono">
                {{ \Illuminate\Support\Str::length($infoText) }} karakter
            </div>
        </div>
        <pre class="text-xs text-slate-200 whitespace-pre-wrap font-mono leading-relaxed max-h-80 overflow-y-auto">{{ $infoText }}</pre>
    </div>
</div>
