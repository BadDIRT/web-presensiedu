@php
    $guard = auth()->guard('web')->check() ? 'web' : (auth()->guard('siswa')->check() ? 'siswa' : null);
    $user = $guard ? auth()->guard($guard)->user() : null;
@endphp
<div x-data="{
    now: new Date(),
    init() {
        setInterval(() => { this.now = new Date(); }, 1000);
    },
    get dayName() {
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        return days[this.now.getDay()];
    },
    get dateStr() {
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        return this.now.getDate() + ' ' + months[this.now.getMonth()] + ' ' + this.now.getFullYear();
    },
    get timeStr() {
        const h = String(this.now.getHours()).padStart(2, '0');
        const m = String(this.now.getMinutes()).padStart(2, '0');
        const s = String(this.now.getSeconds()).padStart(2, '0');
        return h + ':' + m + ':' + s;
    }
}" x-init="init()" class="text-right">
    <div class="text-[10px] uppercase tracking-widest text-slate-400" x-text="dayName + ', ' + dateStr">Memuat...</div>
    <div class="font-display font-bold text-xl text-[#ffcc33] text-glow-gold" x-text="timeStr"
        style="font-variant-numeric: tabular-nums;">--:--:--</div>
</div>
