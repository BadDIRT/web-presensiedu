{{-- Sidebar for admin --}}
@php
    $current = request()->route()->getName();
    $menus = [
        [
            'route' => 'admin.dashboard',
            'label' => 'Dashboard',
            'icon' =>
                '<path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
        ],
        [
            'route' => 'admin.scan',
            'label' => 'Scan QR',
            'icon' => '<path d="M4 4h4m6 0h4M4 4v4m16-4v4M4 16v4m0-4h4m10 4v-4m0 4h-4M4 20h4m6 0h4"/>',
        ],
        [
            'route' => 'admin.students',
            'label' => 'Data Siswa',
            'icon' =>
                '<path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
        ],
        [
            'route' => 'admin.rekap',
            'label' => 'Rekap Harian',
            'icon' => '<path d="M9 17v-2m3 2v-4m3 4v-6M2 5l8-3 8 3M2 5v14l8 3 8-3V5M2 5h16"/>',
        ],
    ];
@endphp

<aside id="sidebar-mobile"
    class="hidden lg:flex flex-col w-60 shrink-0 border-r border-[var(--color-coc-border)] bg-[rgba(13,22,48,0.6)] p-4">
    <div class="mb-4 px-2">
        <span class="text-[10px] uppercase tracking-[0.2em] text-slate-500 font-semibold">Menu Admin</span>
    </div>
    <nav class="flex flex-col gap-1.5">
        @foreach ($menus as $menu)
            @php
                $active = str_starts_with($current, $menu['route']);
            @endphp
            <a href="{{ route($menu['route']) }}"
                class="group relative flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all {{ $active ? 'bg-gradient-to-r from-[rgba(255,204,51,0.18)] to-transparent text-[#ffcc33] border border-[rgba(255,204,51,0.4)]' : 'text-slate-300 hover:bg-white/5 hover:text-white border border-transparent' }}">
                @if ($active)
                    <span
                        class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-7 rounded-r bg-[#ffcc33] shadow-[0_0_8px_#ffcc33]"></span>
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
            <div class="text-[10px] uppercase tracking-widest text-slate-400 mb-1">Mode Belajar</div>
            <div class="text-sm font-semibold text-[#ffcc33] font-display">
                JAM FLEKSIBEL
            </div>
            <div class="text-[11px] text-slate-400 mt-1">Scan QR = Hadir</div>
        </div>
    </div>
</aside>
