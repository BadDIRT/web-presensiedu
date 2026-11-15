<div wire:poll.500ms="refreshState" @answer-judged.window="handleJudged($event.detail.correct)" x-data="playerEffects()">
    {{-- Header --}}
    <div class="coc-hero p-5 mb-6 text-center">
        <span class="title-banner text-sm">ARENA KUIS</span>
        <h1 class="font-display font-black text-2xl mt-3 text-glow-gold">{{ $game->name }}</h1>
        @if ($myGroup)
            <div class="mt-3 inline-flex items-center gap-3 coc-panel-flat p-3">
                <span class="font-display font-bold text-lg"
                    style="color: {{ $myGroup->color }}">{{ $myGroup->name }}</span>
                <span class="text-slate-300 text-sm">{{ $myGroup->leader->name }}</span>
                <span class="text-[#ffcc33] font-display">{{ $myGroup->score }} pts</span>
            </div>
        @endif
    </div>

    {{-- Body sesuai state --}}
    @if (!$myGroup)
        <div class="coc-panel p-8 text-center">
            <div class="text-slate-400">Anda bukan leader di game ini.</div>
        </div>
    @elseif ($game->session_status === 'lobby')
        <div class="coc-panel p-8 text-center">
            <div class="text-slate-400 mb-2">⏳</div>
            <div class="text-sm text-slate-300">Menunggu admin memulai game...</div>
        </div>
    @elseif ($game->session_status === 'question_revealed')
        <div class="coc-panel p-6 mb-4">
            <div class="text-[10px] uppercase tracking-widest text-slate-400 mb-2">Soal Fase {{ $game->current_phase }}
                #{{ $game->current_q_order }}</div>
            <div class="text-lg text-white font-semibold">{{ $game->currentQuestion->question_text }}</div>
        </div>
        <div class="coc-panel p-6 text-center">
            <div class="text-sm text-slate-300">Dengarkan admin membaca soal. Bersiap tekan buzzer!</div>
        </div>
    @elseif ($game->session_status === 'buzzer_open')
        {{-- Soal + tombol buzzer besar --}}
        <div class="coc-panel p-6 mb-4">
            <div class="text-[10px] uppercase tracking-widest text-slate-400 mb-2">Soal</div>
            <div class="text-lg text-white font-semibold">{{ $game->currentQuestion->question_text }}</div>
        </div>
        <div class="text-center py-8">
            <button wire:click="pressBuzzer"
                class="relative w-48 h-48 rounded-full bg-gradient-to-br from-[#ffcc33] to-[#ff4757] text-white font-display font-black text-2xl shadow-[0_0_60px_rgba(255,204,51,0.6)] hover:scale-105 transition-transform active:scale-95"
                style="animation: buzzer-pulse 1s infinite;">
                🔔<br>TEKAN!
            </button>
            <div class="text-sm text-slate-300 mt-4">Tekan secepatnya!</div>
        </div>
    @elseif ($game->session_status === 'buzzer_won')
        @if ($game->buzzer_group_id === $myGroup->id)
            {{-- Kita menang buzzer → input jawaban --}}
            <div class="coc-panel p-6 mb-4">
                <div class="text-[10px] uppercase tracking-widest text-[#22e07b] mb-2">✓ KAMU MENANG BUZZER!</div>
                <div class="text-lg text-white font-semibold">{{ $game->currentQuestion->question_text }}</div>
            </div>
            <form wire:submit="submitAnswer" class="coc-panel p-5 space-y-3">
                <label class="block text-[10px] uppercase tracking-widest text-slate-400">Jawaban</label>
                <input type="text" wire:model="answerText" autofocus required placeholder="Ketik jawaban..."
                    class="coc-input text-lg font-mono">
                <button type="submit" class="btn-coc btn-gold w-full text-base">KIRIM JAWABAN</button>
            </form>
        @else
            {{-- Kelompok lain menang → tunggu --}}
            <div class="coc-panel p-8 text-center">
                <div class="text-4xl mb-3">⏳</div>
                <div class="text-sm text-slate-300">Kelompok <span class="font-bold"
                        style="color: {{ $game->buzzerGroup->color }}">{{ $game->buzzerGroup->name }}</span> lebih
                    dulu tekan buzzer.</div>
                <div class="text-xs text-slate-500 mt-2">Tunggu hasil jawaban...</div>
            </div>
        @endif
    @elseif ($game->session_status === 'judging')
        <div class="coc-panel p-8 text-center">
            <div class="font-display text-2xl text-[#38bdf8] mb-2">⏳ MENILAI...</div>
            @if ($game->buzzer_group_id === $myGroup->id && $game->currentAnswer)
                <div class="text-sm text-slate-300">Jawabanmu: <span
                        class="font-mono text-white">{{ $game->currentAnswer->answer_text }}</span></div>
            @endif
        </div>
    @elseif ($game->session_status === 'result')
        <div class="coc-panel p-8 text-center">
            @if ($game->currentAnswer && $game->currentAnswer->is_correct)
                <div class="font-display font-black text-5xl text-[#22e07b] mb-3">✓ BENAR!</div>
                <div class="text-sm text-slate-300">+{{ $game->currentQuestion->points }} poin untuk kelompokmu!</div>
            @else
                <div class="font-display font-black text-5xl text-[#ff4757] mb-3">✗ SALAH!</div>
                <div class="text-sm text-slate-300">Jawaban benar: <span
                        class="text-[#22e07b] font-mono">{{ $game->currentQuestion->answer_key }}</span></div>
            @endif
            <div class="text-xs text-slate-400 mt-4">Lanjut soal berikutnya dalam {{ $game->secondsUntilNextAction() }}
                detik...</div>
        </div>
    @elseif ($game->session_status === 'finished')
        <div class="coc-panel p-8 text-center">
            <div class="font-display font-black text-3xl text-[#ffcc33] mb-3">🏆 GAME SELESAI</div>
            <div class="text-sm text-slate-300 mb-4">Skor akhir kelompokmu: <span
                    class="font-bold text-[#ffcc33]">{{ $myGroup->score }}</span></div>
            <a href="{{ route('siswa.games.history', $game) }}" class="btn-coc btn-gold">Lihat Hasil</a>
        </div>
    @endif

    {{-- Efek visual global --}}
    <div x-show="showEffect" class="fixed inset-0 z-50 flex items-center justify-center pointer-events-none bg-black/40"
        x-transition.opacity>
        <template x-if="effectType === 'correct'">
            <div class="text-center">
                <div class="text-9xl font-display font-black text-[#22e07b] animate-bounce">✓</div>
                <div class="text-4xl font-display font-black text-[#22e07b] mt-4">BENAR!</div>
            </div>
        </template>
        <template x-if="effectType === 'wrong'">
            <div class="text-center" style="animation: shake 0.5s;">
                <div class="text-9xl font-display font-black text-[#ff4757]">✗</div>
                <div class="text-4xl font-display font-black text-[#ff4757] mt-4">SALAH!</div>
            </div>
        </template>
    </div>

    @push('scripts')
        <script>
            function playerEffects() {
                return {
                    showEffect: false,
                    effectType: '',
                    init() {
                        window.addEventListener('answer-judged', (e) => this.handleJudged(e.detail.correct));
                    },
                    handleJudged(correct) {
                        setTimeout(() => {
                            this.effectType = correct ? 'correct' : 'wrong';
                            this.showEffect = true;
                            if (correct) this.playCorrectSound();
                            else this.playWrongSound();
                            setTimeout(() => this.showEffect = false, 3000);
                        }, 5000);
                    },
                    playCorrectSound() {
                        const ctx = new(window.AudioContext || window.webkitAudioContext)();
                        const now = ctx.currentTime;
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
                    }
                };
            }
        </script>
        <style>
            @keyframes buzzer-pulse {

                0%,
                100% {
                    transform: scale(1);
                    box-shadow: 0 0 60px rgba(255, 204, 51, 0.6);
                }

                50% {
                    transform: scale(1.05);
                    box-shadow: 0 0 80px rgba(255, 71, 87, 0.8);
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
