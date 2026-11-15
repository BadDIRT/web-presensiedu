<div class="coc-panel p-6">
    <div class="text-center mb-6">
        <span class="title-banner">🏆 LEADERBOARD</span>
        <h1 class="font-display font-black text-3xl mt-3 text-glow-gold">{{ $game->name }}</h1>
        <p class="text-sm text-slate-300 mt-1">Permainan selesai! Inilah peringkat akhir kelompok.</p>
    </div>

    <div class="space-y-3 max-w-2xl mx-auto">
        @php
            $rank = 0;
            $prevScore = null;
        @endphp
        @foreach ($groups as $i => $g)
            @php
                if ($prevScore === null || $g->score !== $prevScore) {
                    $rank = $i + 1;
                }
                $prevScore = $g->score;
            @endphp
            <div class="coc-panel-flat p-4 flex items-center gap-4 {{ $rank === 1 ? 'border-[#ffcc33] border-2' : '' }}">
                <div
                    class="font-display font-black text-3xl w-12 text-center
                    {{ $rank === 1 ? 'text-[#ffcc33]' : ($rank === 2 ? 'text-slate-300' : ($rank === 3 ? 'text-[#ff9d2e]' : 'text-slate-500')) }}">
                    {{ $rank === 1 ? '🥇' : ($rank === 2 ? '🥈' : ($rank === 3 ? '🥉' : $rank)) }}
                </div>
                <div class="flex-1">
                    <div class="font-display font-bold text-xl" style="color: {{ $g->color }}">{{ $g->name }}
                    </div>
                    <div class="text-xs text-slate-400">{{ $g->leader->name }}</div>
                </div>
                <div class="text-right">
                    <div class="font-display font-black text-2xl text-[#ffcc33]">{{ $g->score }}</div>
                    <div class="text-[10px] text-slate-500">POIN</div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6 text-center">
        <a href="{{ route('admin.games.index') }}" class="btn-coc btn-ghost">← Kembali ke Daftar Game</a>
    </div>
</div>
