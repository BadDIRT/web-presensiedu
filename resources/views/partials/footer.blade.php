<footer class="mt-auto border-t border-[var(--color-coc-border)] bg-[rgba(7,9,26,0.85)] py-4 px-4">
    <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-500">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-[#ffcc33] shadow-[0_0_6px_#ffcc33]"></span>
            <span class="font-display tracking-widest">PRESENSIEDU · CLASH OF CHAMPIONS</span>
        </div>
        <div class="text-[11px]">
            {{ date('Y') }} · Sistem Absensi Sekolah · Laravel {{ app()->version() }}
        </div>
    </div>
</footer>
