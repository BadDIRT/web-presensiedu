@extends('layouts.app')

@section('title', $game->name)

@section('layout-body')
    @include('partials.sidebar-admin')

    <main class="flex-1 p-4 sm:p-6 lg:p-8">
        <div class="coc-hero p-5 mb-6">
            <span class="title-banner text-sm">{{ strtoupper($game->name) }}</span>
            <h1 class="font-display font-black text-2xl mt-3 text-glow-gold">DETAIL GAME</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="coc-panel p-5">
                <div class="text-[10px] uppercase tracking-widest text-slate-400 mb-2">Kelompok</div>
                @foreach ($game->groups as $g)
                    <div class="coc-panel-flat p-3 mb-2 flex items-center justify-between">
                        <div>
                            <div class="font-bold" style="color: {{ $g->color }}">{{ $g->name }}</div>
                            <div class="text-xs text-slate-400">{{ $g->leader->name }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-[10px] text-slate-500">Skor</div>
                            <div class="font-display text-xl text-[#ffcc33]">{{ $g->score }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="coc-panel p-5 lg:col-span-2">
                <div class="text-[10px] uppercase tracking-widest text-slate-400 mb-2">Daftar Soal</div>
                @foreach ($game->questions->groupBy('phase') as $phase => $qs)
                    <div class="mb-4">
                        <div class="font-display text-[#ffcc33] font-bold mb-2">FASE {{ $phase }}</div>
                        <div class="space-y-2">
                            @foreach ($qs as $q)
                                <div class="coc-panel-flat p-3">
                                    <div class="text-xs text-slate-400">{{ $phase }}.{{ $q->order }} ·
                                        {{ $q->points }} pts</div>
                                    <div class="text-sm text-white">{{ $q->question_text }}</div>
                                    <div class="text-xs text-[#22e07b] mt-1">Jawaban: {{ $q->answer_key }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-6 flex gap-2">
            @if ($game->status === 'draft')
                <a href="{{ route('admin.games.host', $game) }}" class="btn-coc btn-gold">🚀 Mulai Game</a>
            @elseif ($game->status === 'active')
                <a href="{{ route('admin.games.host', $game) }}" class="btn-coc btn-red">▶ Lanjutkan</a>
            @else
                <a href="{{ route('admin.games.leaderboard', $game) }}" class="btn-coc btn-blue">🏆 Lihat Hasil</a>
            @endif
            <a href="{{ route('admin.games.index') }}" class="btn-coc btn-ghost">← Kembali</a>
        </div>
    </main>
@endsection
