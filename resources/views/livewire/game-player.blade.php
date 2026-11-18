<div wire:poll.500ms="refreshState" @answer-judged.window="handleJudged($event.detail.correct)"
    @buzzer-pressed.window="playBuzzerPressSound()" @answer-submitted.window="playSubmitSound()" x-data="playerEffects()">
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
            <div class="text-slate-400 mb-2 text-5xl">⏳</div>
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
            <div class="coc-panel p-8 text-center">
                <div class="text-4xl mb-3">⏳</div>
                <div class="text-sm text-slate-300">Kelompok <span class="font-bold"
                        style="color: {{ $game->buzzerGroup->color }}">{{ $game->buzzerGroup->name }}</span> lebih
                    dulu tekan buzzer.</div>
                <div class="text-xs text-slate-500 mt-2">Tunggu hasil jawaban...</div>
            </div>
        @endif
    @elseif ($game->session_status === 'judging')
        {{-- Countdown 5 detik menunggu hasil --}}
        <div class="coc-panel p-8 text-center" x-data="{ countdown: 5 }" x-init="setInterval(() => { if (countdown > 0) countdown--; }, 1000)">
            <div class="font-display text-2xl text-[#38bdf8] mb-3">⏳ MENUNGGU HASIL...</div>
            @if ($game->buzzer_group_id === $myGroup->id && $game->currentAnswer)
                <div class="coc-panel-flat p-3 mb-3 inline-block">
                    <div class="text-[10px] uppercase text-slate-500">Jawabanmu</div>
                    <div class="text-lg text-white font-mono">{{ $game->currentAnswer->answer_text }}</div>
                </div>
            @endif
            <div class="font-display text-6xl text-[#ffcc33] text-glow-gold my-4" x-text="countdown">5</div>
            <div class="text-xs text-slate-400">Hasil akan diumumkan...</div>
        </div>
    @elseif ($game->session_status === 'result')
        {{-- Card result muncul SETELAH overlay selesai --}}
        <div class="coc-panel p-8 text-center" x-show="!showOverlay" x-transition.opacity>
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

    {{-- Overlay notif BENAR/SALAH --}}
    <div x-show="showOverlay"
        class="fixed inset-0 z-50 flex items-center justify-center pointer-events-none bg-black/40 backdrop-blur-sm"
        x-transition.opacity>
        <template x-if="overlayType === 'correct'">
            <div class="text-center" style="animation: popIn 0.5s;">
                <div
                    class="text-[10rem] font-display font-black text-[#22e07b] text-glow-blue animate-bounce leading-none">
                    ✓</div>
                <div class="text-6xl font-display font-black text-[#22e07b] text-glow-blue mt-4">BENAR!</div>
                <div class="text-3xl mt-4">🎂🎉🎊</div>
            </div>
        </template>
        <template x-if="overlayType === 'wrong'">
            <div class="text-center" style="animation: shake 0.5s;">
                <div class="text-[10rem] font-display font-black text-[#ff4757] text-glow-red leading-none">✗</div>
                <div class="text-6xl font-display font-black text-[#ff4757] text-glow-red mt-4">SALAH!</div>
            </div>
        </template>
    </div>

    @push('scripts')
        <script>
            function playerEffects() {
                return {
                    showOverlay: false,
                    overlayType: '',
                    init() {},
                    handleJudged(correct) {
                        this.overlayType = correct ? 'correct' : 'wrong';
                        this.showOverlay = true;

                        if (correct) this.playBirthdayFanfare();
                        else this.playWrongHeboh();

                        // Sembunyikan overlay setelah 4 detik → card result muncul
                        setTimeout(() => {
                            this.showOverlay = false;
                        }, 4000);
                    },
                    playBuzzerPressSound() {
                        const ctx = new(window.AudioContext || window.webkitAudioContext)();
                        const now = ctx.currentTime;

                        // "DING!" bell
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.type = 'sine';
                        osc.frequency.setValueAtTime(1400, now);
                        osc.frequency.exponentialRampToValueAtTime(900, now + 0.15);
                        gain.gain.setValueAtTime(0.4, now);
                        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.4);
                        osc.start(now);
                        osc.stop(now + 0.4);

                        // Click sound
                        const click = ctx.createOscillator();
                        const clickGain = ctx.createGain();
                        click.connect(clickGain);
                        clickGain.connect(ctx.destination);
                        click.type = 'square';
                        click.frequency.value = 3000;
                        clickGain.gain.setValueAtTime(0.3, now);
                        clickGain.gain.exponentialRampToValueAtTime(0.001, now + 0.05);
                        click.start(now);
                        click.stop(now + 0.05);
                    },
                    playSubmitSound() {
                        const ctx = new(window.AudioContext || window.webkitAudioContext)();
                        const now = ctx.currentTime;
                        // "Swoosh" + beep
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.type = 'sine';
                        osc.frequency.setValueAtTime(400, now);
                        osc.frequency.linearRampToValueAtTime(1200, now + 0.2);
                        gain.gain.setValueAtTime(0.3, now);
                        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.3);
                        osc.start(now);
                        osc.stop(now + 0.3);
                    },
                    playBirthdayFanfare() {
                        const ctx = new(window.AudioContext || window.webkitAudioContext)();
                        const now = ctx.currentTime;

                        // === 1. FANFARE TRUMPET (do-mi-sol-do) ===
                        const notes = [523.25, 659.25, 783.99, 1046.50];
                        for (let round = 0; round < 2; round++) {
                            notes.forEach((freq, i) => {
                                const startTime = now + round * 0.5 + i * 0.1;
                                [0, 5, -5].forEach(detune => {
                                    const osc = ctx.createOscillator();
                                    const gain = ctx.createGain();
                                    osc.connect(gain);
                                    gain.connect(ctx.destination);
                                    osc.type = 'sawtooth';
                                    osc.frequency.value = freq * Math.pow(2, detune / 1200);
                                    gain.gain.setValueAtTime(0, startTime);
                                    gain.gain.linearRampToValueAtTime(0.18, startTime + 0.02);
                                    gain.gain.exponentialRampToValueAtTime(0.001, startTime + 0.35);
                                    osc.start(startTime);
                                    osc.stop(startTime + 0.35);
                                });
                            });
                        }

                        // === 2. PARTY HORN / TEROMPET ULANG TAHUN ===
                        const noiseBuffer = ctx.createBuffer(1, ctx.sampleRate * 1.5, ctx.sampleRate);
                        const data = noiseBuffer.getChannelData(0);
                        for (let i = 0; i < data.length; i++) {
                            data[i] = (Math.random() * 2 - 1) * Math.exp(-i / (ctx.sampleRate * 0.4));
                        }
                        const noise = ctx.createBufferSource();
                        noise.buffer = noiseBuffer;
                        const noiseFilter = ctx.createBiquadFilter();
                        noiseFilter.type = 'bandpass';
                        noiseFilter.frequency.setValueAtTime(800, now + 0.4);
                        noiseFilter.frequency.linearRampToValueAtTime(2500, now + 1.2);
                        noiseFilter.Q.value = 3;
                        const noiseGain = ctx.createGain();
                        noiseGain.gain.setValueAtTime(0, now + 0.4);
                        noiseGain.gain.linearRampToValueAtTime(0.4, now + 0.45);
                        noiseGain.gain.exponentialRampToValueAtTime(0.001, now + 1.9);
                        noise.connect(noiseFilter);
                        noiseFilter.connect(noiseGain);
                        noiseGain.connect(ctx.destination);
                        noise.start(now + 0.4);

                        // === 3. WHISTLES (cheer) ===
                        for (let w = 0; w < 4; w++) {
                            const ws = ctx.createOscillator();
                            const wg = ctx.createGain();
                            ws.connect(wg);
                            wg.connect(ctx.destination);
                            ws.type = 'sine';
                            const startFreq = 1800 + w * 300;
                            ws.frequency.setValueAtTime(startFreq, now + 1.5 + w * 0.1);
                            ws.frequency.linearRampToValueAtTime(startFreq + 800, now + 1.9 + w * 0.1);
                            wg.gain.setValueAtTime(0, now + 1.5 + w * 0.1);
                            wg.gain.linearRampToValueAtTime(0.12, now + 1.55 + w * 0.1);
                            wg.gain.exponentialRampToValueAtTime(0.001, now + 2.0 + w * 0.1);
                            ws.start(now + 1.5 + w * 0.1);
                            ws.stop(now + 2.0 + w * 0.1);
                        }

                        // === 4. FINAL "YAY!" CHORD ===
                        [523.25, 659.25, 783.99].forEach(freq => {
                            const osc = ctx.createOscillator();
                            const gain = ctx.createGain();
                            osc.connect(gain);
                            gain.connect(ctx.destination);
                            osc.type = 'triangle';
                            osc.frequency.value = freq;
                            gain.gain.setValueAtTime(0, now + 2.0);
                            gain.gain.linearRampToValueAtTime(0.2, now + 2.05);
                            gain.gain.exponentialRampToValueAtTime(0.001, now + 3.0);
                            osc.start(now + 2.0);
                            osc.stop(now + 3.0);
                        });
                    },
                    playWrongHeboh() {
                        const ctx = new(window.AudioContext || window.webkitAudioContext)();
                        const now = ctx.currentTime;

                        // "Wah wah wah" trombone
                        const notes = [466.16, 415.30, 369.99, 311.13];
                        notes.forEach((freq, i) => {
                            const startTime = now + i * 0.18;
                            const osc = ctx.createOscillator();
                            const gain = ctx.createGain();
                            osc.connect(gain);
                            gain.connect(ctx.destination);
                            osc.type = 'sawtooth';
                            osc.frequency.setValueAtTime(freq, startTime);
                            osc.frequency.linearRampToValueAtTime(freq * 0.95, startTime + 0.15);
                            gain.gain.setValueAtTime(0, startTime);
                            gain.gain.linearRampToValueAtTime(0.3, startTime + 0.02);
                            gain.gain.exponentialRampToValueAtTime(0.001, startTime + 0.16);
                            osc.start(startTime);
                            osc.stop(startTime + 0.16);
                        });

                        // Buzzer bass
                        const buzz = ctx.createOscillator();
                        const buzzGain = ctx.createGain();
                        buzz.connect(buzzGain);
                        buzzGain.connect(ctx.destination);
                        buzz.type = 'square';
                        buzz.frequency.setValueAtTime(120, now);
                        buzz.frequency.linearRampToValueAtTime(80, now + 1.0);
                        buzzGain.gain.setValueAtTime(0.15, now);
                        buzzGain.gain.exponentialRampToValueAtTime(0.001, now + 1.0);
                        buzz.start(now);
                        buzz.stop(now + 1.0);

                        // "TETTTT" alarm
                        setTimeout(() => {
                            for (let i = 0; i < 3; i++) {
                                const o2 = ctx.createOscillator();
                                const g2 = ctx.createGain();
                                o2.connect(g2);
                                g2.connect(ctx.destination);
                                o2.type = 'square';
                                o2.frequency.value = 220;
                                const t = ctx.currentTime + i * 0.15;
                                g2.gain.setValueAtTime(0.3, t);
                                g2.gain.exponentialRampToValueAtTime(0.001, t + 0.1);
                                o2.start(t);
                                o2.stop(t + 0.1);
                            }
                        }, 500);
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
                    transform: translateX(-15px);
                }

                40% {
                    transform: translateX(15px);
                }

                60% {
                    transform: translateX(-15px);
                }

                80% {
                    transform: translateX(15px);
                }
            }

            @keyframes popIn {
                0% {
                    transform: scale(0);
                    opacity: 0;
                }

                50% {
                    transform: scale(1.2);
                    opacity: 1;
                }

                100% {
                    transform: scale(1);
                    opacity: 1;
                }
            }
        </style>
    @endpush
</div>
