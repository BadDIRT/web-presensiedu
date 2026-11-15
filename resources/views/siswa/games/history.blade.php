@extends('layouts.app')

@section('title', 'Hasil Game')

@section('layout-body')
    @include('partials.sidebar-siswa')

    <main class="flex-1 p-4 sm:p-6 lg:p-8">
        <div class="coc-panel p-6 max-w-lg mx-auto text-center">
            <span class="title-banner">HASIL GAME</span>
            <h1 class="font-display font-black text-2xl mt-3 text-glow-blue">{{ $game->name }}</h1>

            @if ($history)
                <div class="my-6">
                    <div class="text-[10px] uppercase tracking-widest text-slate-400">Skor Akhir</div>
                    <div class="font-display font-black text-6xl text-[#ffcc33] text-glow-gold my-2">{{ $history->score }}
                    </div>
                    <div class="text-sm text-slate-300">Peringkat #{{ $history->rank }}</div>
                </div>
            @else
                <div class="my-6 text-slate-400">Belum ada data history.</div>
            @endif

            <a href="{{ route('siswa.games.index') }}" class="btn-coc btn-ghost">← Kembali</a>
        </div>
    </main>
@endsection
