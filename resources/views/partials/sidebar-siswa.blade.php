{{-- Sidebar for siswa --}}
@php
    $current = request()->route()->getName();
    $menus = [
        ['route' => 'siswa.dashboard', 'label' => 'Dashboard', 'icon' => '<path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>'],
        ['route' => 'siswa.qr',        'label' => 'QR Code Saya', 'icon' => '<path d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M3 9h2m-2 6h2M7 4h2M7 20h2m-4-7v3a2 2 0 002 2h10a2 2 0 002-2V8a2 2 0 00-2-2H7a2 2 0 00-2 2v3z"/>'],
        ['route' => 'siswa.riwayat',    'label' => 'Riwayat Absen', 'icon' => '<path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>'],
    ];
@endphp

<aside id="sidebar-mobile"
       class="hidden lg:flex flex-col w-60 shrink-0 border-r border-[var(--color-coc-border)] bg-[rgba(13,22,48,0.6)] p-4">
    <div class="mb-4 px-2">
        <span class="text-[10px] uppercase tracking-[0.2em] text-slate-500 font-semibold">Menu Siswa</span>
    </div>
    <nav class="flex flex-col gap-1.5">
        @foreach ($menus as $menu)
            @php
                $active = str_starts_with($current, $menu['route']);
            @endphp
            <a href="{{ route($menu['route']) }}"
               class="group relative flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all {{ $active ? 'bg-gradient-to-r from-[rgba(56,189,248,0.18)] to-transparent text-[#38bdf8] border border-[rgba(56,189,248,0.4)]' : 'text-slate-300 hover:bg-white/5 hover:text-white border border-transparent' }}">
                @if ($active)
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-7 rounded-r bg-[#38bdf8] shadow-[0_0_8px_#38bdf8]"></span>
                @endif
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    {!! $menu['icon'] !!}
                </svg>
                <span class="font-semibold text-sm">{{ $menu['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="mt-auto pt-4 border-t border-[var(--color-coc-border)]">
        <div class="coc-panel-flat p-3">
            <div class="text-[10px] uppercase tracking-widest text-slate-400 mb-1">NIS Saya</div>
            <div class="text-sm font-semibold text-[#38bdf8] font-display tracking-widest">{{ auth()->user()->nis }}</div>
            <div class="text-[11px] text-slate-400 mt-1">{{ auth()->user()->kelas ?? '-' }}</div>
        </div>
    </div>
</aside>
