{{-- Top banner: clash of champions style --}}
<header
    class="relative z-20 sticky top-0 border-b border-[var(--color-coc-border)] bg-[rgba(7,9,26,0.85)] backdrop-blur-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between gap-3">
        {{-- Brand --}}
        <div class="flex items-center gap-3">
            <button class="lg:hidden btn-ghost px-2 py-2 rounded-md"
                onclick="document.getElementById('sidebar-mobile').classList.toggle('hidden')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            <div class="flex items-center gap-2 sm:gap-3">
                <div class="relative">
                    <div
                        class="w-10 h-10 sm:w-11 sm:h-11 rounded-lg bg-gradient-to-br from-[#ffcc33] to-[#ff4757] flex items-center justify-center font-display font-black text-[#1a1306] text-lg sm:text-xl shadow-lg">
                        PE
                    </div>
                    <span
                        class="absolute -top-1 -right-1 w-3 h-3 rounded-full bg-[#22e07b] ring-2 ring-[#07091a]"></span>
                </div>
                <div>
                    <h1 class="font-display font-bold text-base sm:text-lg leading-tight text-glow-gold">PRESENSIEDU
                    </h1>
                    <p class="text-[10px] sm:text-[11px] uppercase tracking-widest text-slate-400">Clash of Champions
                    </p>
                </div>
            </div>
        </div>

        {{-- Right side --}}
        <div class="flex items-center gap-2 sm:gap-4">
            @php
                $guard = auth()->guard('web')->check() ? 'web' : (auth()->guard('siswa')->check() ? 'siswa' : null);
                $user = $guard ? auth()->guard($guard)->user() : null;
            @endphp
            @if ($user)
                <div class="flex items-center gap-2 sm:gap-4">
                    <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 coc-panel-flat rounded-lg">
                        <span class="live-dot"></span>
                        <span class="text-xs uppercase tracking-wider text-slate-300 font-semibold">LIVE</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <div class="text-right hidden sm:block">
                            <div class="text-sm font-semibold leading-tight">{{ $user->name }}</div>
                            <div class="text-[11px] text-slate-400">
                                @if ($guard === 'web')
                                    @if ($user->isAdmin())
                                        <span class="text-[#ffcc33]">⚔ ADMIN</span>
                                    @else
                                        <span class="text-[#38bdf8]">♟ SISWA</span>
                                    @endif
                                @else
                                    <span class="text-[#38bdf8]">♟ SISWA</span>
                                    @if (!empty($user->kelas))
                                        · {{ $user->kelas }}
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div
                            class="w-9 h-9 rounded-lg bg-gradient-to-br from-[#1b2547] to-[#0e1530] border border-[var(--color-coc-border)] flex items-center justify-center font-display font-bold text-sm">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="btn-coc btn-red !py-2 !px-3" title="Logout">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                </path>
                            </svg>
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</header>
