@extends('layouts.app')

@section('title', 'Game Saya')

@section('layout-body')
    @include('partials.sidebar-siswa')

    <main class="flex-1 p-4 sm:p-6 lg:p-8">
        <div class="coc-hero p-5 mb-6">
            <span class="title-banner text-sm">ARENA KUIS</span>
            <h1 class="font-display font-black text-2xl mt-3 text-glow-blue">GAME KUIS</h1>
            <p class="text-sm text-slate-300 mt-1">Game di mana kamu jadi leader kelompok.</p>
        </div>

        <div class="coc-panel p-5 mb-6">
            <div class="text-[10px] uppercase tracking-widest text-slate-400 mb-3">Game Aktif</div>
            @forelse ($activeGames as $game)
                @php
                    $myGroup = $game->groups->where('leader_id', auth()->guard('siswa')->id())->first();
                @endphp
                <div class="coc-panel-flat p-4 mb-2 flex items-center justify-between">
                    <div>
                        <div class="font-display font-bold text-lg text-white">{{ $game->name }}</div>
                        <div class="text-xs text-slate-400">
                            Kelompok <span class="font-bold"
                                style="color: {{ $myGroup->color }}">{{ $myGroup->name }}</span>
                            · Status: <span class="uppercase">{{ $game->status }}</span>
                        </div>
                    </div>
                    <a href="{{ route('siswa.games.play', $game) }}" class="btn-coc btn-gold !py-2 text-xs">
                        {{ $game->session_status === 'lobby' ? 'Masuk Lobby' : 'Lanjut Game' }}
                    </a>
                </div>
            @empty
                <div class="text-center py-6 text-slate-500 text-sm">
                    Belum ada game aktif yang menugasmu sebagai leader.
                </div>
            @endforelse
        </div>

        <div class="coc-panel p-5">
            <div class="text-[10px] uppercase tracking-widest text-slate-400 mb-3">Riwayat Game</div>
            @forelse ($pastGames as $h)
                <div class="coc-panel-flat p-3 mb-2 flex items-center justify-between">
                    <div>
                        <div class="font-semibold text-white text-sm">{{ $h->game_name }}</div>
                        <div class="text-xs text-slate-400">{{ $h->played_at->format('d M Y H:i') }}</div>
                    </div>
                    <div class="text-right">
                        <div class="font-display font-bold text-lg text-[#ffcc33]">{{ $h->score }}</div>
                        <div class="text-[10px] text-slate-500">Peringkat #{{ $h->rank }}</div>
                    </div>
                </div>
            @empty
                <div class="text-center py-6 text-slate-500 text-sm">Belum ada riwayat.</div>
            @endforelse
        </div>
    </main>
@endsection
