<div wire:poll.500ms="refreshState" @answer-judged.window="handleJudged($event.detail.correct)"
    @game-ended.window="window.location.href='{{ route('admin.games.leaderboard', $game->id) }}'" x-data="hostEffects()"
    x-init="init()">
    {{-- Header --}}
    <div class="coc-hero p-5 mb-6 relative overflow-hidden">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <span class="title-banner text-sm">HOST GAME</span>
                <h1 class="font-display font-black text-2xl mt-3 text-glow-gold">{{ $game->name }}</h1>
                <p class="text-sm text-slate-300 mt-1">Status: <span
                        class="text-[#ffcc33] font-bold uppercase">{{ str_replace('_', ' ', $game->session_status) }}</span>
                </p>
            </div>
            <div class="text-right">
                <div class="text-[10px] uppercase tracking-widest text-slate-400">Fase</div>
                <div class="font-display font-bold text-xl text-[#ffcc33]">{{ $game->current_phase }} / 3</div>
                <div class="text-xs text-slate-400 mt-1">Soal {{ $game->current_q_order }} / 10</div>
            </div>
        </div>
    </div>

    {{-- Skor live --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        @foreach ($game->groups as $g)
            <div class="stat-card">
                <div class="text-[10px] uppercase tracking-widest text-slate-400" style="color: {{ $g->color }}">
                    {{ $g->name }}</div>
                <div class="font-display font-black text-3xl text-white mt-1">{{ $g->score }}</div>
                <div class="text-[10px] text-slate-500 mt-0.5">{{ $g->leader->name }}</div>
            </div>
        @endforeach
    </div>

    {{-- Soal saat ini --}}
    @if ($game->currentQuestion)
        <div class="coc-panel coc-corner p-6 mb-6">
            <div class="flex items-center justify-between mb-3">
                <span class="title-banner text-sm">SOAL FASE {{ $game->current_phase }}
                    #{{ $game->current_q_order }}</span>
                <span class="text-xs text-slate-400">{{ $game->currentQuestion->points }} pts</span>
            </div>
            <div class="text-lg text-white font-semibold">{{ $game->currentQuestion->question_text }}</div>
            <div class="mt-2 text-xs text-slate-500">Kunci: <span
                    class="text-[#22e07b] font-mono">{{ $game->currentQuestion->answer_key }}</span></div>
        </div>
    @endif

    {{-- Kontrol sesuai state --}}
    <div class="coc-panel p-5">
        @if ($game->session_status === 'lobby')
            <div class="text-center py-6">
                <div class="text-sm text-slate-300 mb-4">Game siap dimulai. Pastikan semua leader sudah membuka halaman
                    play di HP mereka.</div>
                <button wire:click="startGame" class="btn-coc btn-gold text-base">🚀 MULAI GAME</button>
            </div>
        @elseif ($game->session_status === 'question_revealed')
            <div class="text-center py-6">
                <div class="text-sm text-slate-300 mb-2">Soal sudah ditampilkan ke leader. Baca soal lalu buka buzzer.
                </div>
                <button wire:click="openBuzzer" class="btn-coc btn-gold text-base">🔔 BUKA BUZZER</button>
            </div>
        @elseif ($game->session_status === 'buzzer_open')
            <div class="text-center py-6">
                <div class="font-display text-2xl text-[#ffcc33] text-glow-gold mb-2 pulse-anim">⏳ MENUNGGU BUZZER...
                </div>
                <div class="text-sm text-slate-300">Leader mana yang paling cepat tekan tombol?</div>
            </div>
        @elseif ($game->session_status === 'buzzer_won')
            <div class="text-center py-6">
                <div class="text-sm text-slate-300 mb-2">Kelompok yang menang buzzer:</div>
                <div class="font-display font-black text-3xl mb-4" style="color: {{ $game->buzzerGroup->color }}">
                    {{ $game->buzzerGroup->name }}</div>
                <div class="text-sm text-slate-300">Menunggu jawaban dari leader...</div>
            </div>
        @elseif ($game->session_status === 'judging')
            <div class="text-center py-6">
                <div class="font-display text-2xl text-[#38bdf8] mb-3">⏳ MENILAI JAWABAN...</div>
                @if ($game->currentAnswer)
                    <div class="coc-panel-flat p-3 mb-3 inline-block">
                        <div class="text-[10px] uppercase text-slate-500">Jawaban Leader</div>
                        <div class="text-lg text-white font-mono">{{ $game->currentAnswer->answer_text }}</div>
                    </div>
                @endif
            </div>
        @elseif ($game->session_status === 'result')
            <div class="text-center py-6">
                @if ($game->currentAnswer && $game->currentAnswer->is_correct)
                    <div class="font-display font-black text-4xl text-[#22e07b] mb-2">✓ BENAR!</div>
                    <div class="text-sm text-slate-300">+{{ $game->currentQuestion->points }} poin untuk
                        {{ $game->buzzerGroup->name }}</div>
                @else
                    <div class="font-display font-black text-4xl text-[#ff4757] mb-2">✗ SALAH!</div>
                    <div class="text-sm text-slate-300">Jawaban: <span
                            class="text-[#22e07b]">{{ $game->currentQuestion->answer_key }}</span></div>
                @endif
                <div class="text-xs text-slate-400 mt-4">Lanjut ke soal berikutnya dalam
                    {{ $game->secondsUntilNextAction() }} detik...</div>
                <button wire:click="skipQuestion" class="btn-coc btn-ghost mt-3 text-xs">Lewati sekarang →</button>
            </div>
        @elseif ($game->session_status === 'finished')
            <div class="text-center py-6">
                <div class="font-display font-black text-3xl text-[#ffcc33] mb-2">🏆 GAME SELESAI</div>
                <a href="{{ route('admin.games.leaderboard', $game) }}" class="btn-coc btn-gold">Lihat Leaderboard</a>
            </div>
        @endif
    </div>

    {{-- Hasil jawaban + efek visual --}}
    <div x-show="showEffect" class="fixed inset-0 z-50 flex items-center justify-center pointer-events-none"
        x-transition.opacity>
        <template x-if="effectType === 'correct'">
            <div class="text-center">
                <div class="text-9xl font-display font-black text-[#22e07b] text-glow-blue animate-bounce">✓</div>
                <div class="text-4xl font-display font-black text-[#22e07b] mt-4">BENAR!</div>
            </div>
        </template>
        <template x-if="effectType === 'wrong'">
            <div class="text-center" style="animation: shake 0.5s;">
                <div class="text-9xl font-display font-black text-[#ff4757] text-glow-red">✗</div>
                <div class="text-4xl font-display font-black text-[#ff4757] mt-4">SALAH!</div>
            </div>
        </template>
    </div>

    {{-- Confetti container --}}
    <div id="confetti" class="fixed inset-0 z-40 pointer-events-none"></div>

    @push('scripts')
        <script>
            function hostEffects() {
                return {
                    showEffect: false,
                    effectType: '',
                    init() {
                        // listen event Livewire
                        window.addEventListener('answer-judged', (e) => this.handleJudged(e.detail.correct));
                    },
                    handleJudged(correct) {
                        // Hitung countdown 5 detik dulu sebelum tampil
                        setTimeout(() => {
                            this.effectType = correct ? 'correct' : 'wrong';
                            this.showEffect = true;

                            // Play sound
                            if (correct) this.playCorrectSound();
                            else this.playWrongSound();

                            // Confetti kalau benar
                            if (correct) this.fireConfetti();

                            // Sembunyikan setelah 3 detik
                            setTimeout(() => this.showEffect = false, 3000);
                        }, 5000); // 5 detik countdown dulu
                    },
                    playCorrectSound() {
                        const ctx = new(window.AudioContext || window.webkitAudioContext)();
                        const now = ctx.currentTime;
                        // C-E-G ascending chord
                        [523.25, 659.25, 783.99, 1046.50].forEach((freq, i) => {
                            const osc = ctx.createOscillator();
                            const gain = ctx.createGain();
                            osc.connect(gain);
                            gain.connect(ctx.destination);
                            osc.frequency.value = freq;
                            osc.type = 'sine';
                            gain.gain.setValueAtTime(0, now + i * 0.08);
                            gain.gain.linearRampToValueAtTime(0.3, now + i * 0.08 + 0.04);
                            gain.gain.exponentialRampToValueAtTime(0.001, now + i * 0.08 + 0.7);
                            osc.start(now + i * 0.08);
                            osc.stop(now + i * 0.08 + 0.7);
                        });
                    },
                    playWrongSound() {
                        const ctx = new(window.AudioContext || window.webkitAudioContext)();
                        const now = ctx.currentTime;
                        // Buzz descending
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.type = 'sawtooth';
                        osc.frequency.setValueAtTime(220, now);
                        osc.frequency.exponentialRampToValueAtTime(80, now + 0.8);
                        gain.gain.setValueAtTime(0.3, now);
                        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.8);
                        osc.start(now);
                        osc.stop(now + 0.8);
                        // Tambahan "tettt" beep kedua
                        setTimeout(() => {
                            const o2 = ctx.createOscillator();
                            const g2 = ctx.createGain();
                            o2.connect(g2);
                            g2.connect(ctx.destination);
                            o2.type = 'square';
                            o2.frequency.value = 200;
                            g2.gain.setValueAtTime(0.25, ctx.currentTime);
                            g2.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.4);
                            o2.start();
                            o2.stop(ctx.currentTime + 0.4);
                        }, 300);
                    },
                    fireConfetti() {
                        const colors = ['#ffcc33', '#ff4757', '#38bdf8', '#22e07b', '#a855f7'];
                        const container = document.getElementById('confetti');
                        for (let i = 0; i < 80; i++) {
                            const c = document.createElement('div');
                            c.style.cssText = `
                        position: absolute;
                        left: ${Math.random() * 100}%;
                        top: -20px;
                        width: ${5 + Math.random() * 10}px;
                        height: ${5 + Math.random() * 10}px;
                        background: ${colors[Math.floor(Math.random() * colors.length)]};
                        opacity: 0.9;
                        transform: rotate(${Math.random() * 360}deg);
                        animation: confetti-fall ${1.5 + Math.random() * 1}s linear forwards;
                    `;
                            container.appendChild(c);
                            setTimeout(() => c.remove(), 3000);
                        }
                    }
                };
            }
        </script>
        <style>
            @keyframes confetti-fall {
                to {
                    transform: translateY(110vh) rotate(720deg);
                    opacity: 0;
                }
            }

            @keyframes shake {

                0%,
                100% {
                    transform: translateX(0);
                }

                20% {
                    transform: translateX(-10px);
                }

                40% {
                    transform: translateX(10px);
                }

                60% {
                    transform: translateX(-10px);
                }

                80% {
                    transform: translateX(10px);
                }
            }
        </style>
    @endpush
</div>
