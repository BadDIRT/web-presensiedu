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

    {{-- Soal saat ini (TANPA kunci jawaban) --}}
    @if ($game->currentQuestion)
        <div class="coc-panel coc-corner p-6 mb-6">
            <div class="flex items-center justify-between mb-3">
                <span class="title-banner text-sm">SOAL FASE {{ $game->current_phase }}
                    #{{ $game->current_q_order }}</span>
                <span class="text-xs text-slate-400">{{ $game->currentQuestion->points }} pts</span>
            </div>
            <div class="text-lg text-white font-semibold">{{ $game->currentQuestion->question_text }}</div>
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
            <div class="text-center py-6" x-data="{ countdown: 5 }" x-init="setInterval(() => { if (countdown > 0) countdown--; }, 1000)">
                <div class="font-display text-2xl text-[#38bdf8] mb-3">⏳ MENILAI JAWABAN...</div>
                @if ($game->currentAnswer)
                    <div class="coc-panel-flat p-3 mb-3 inline-block">
                        <div class="text-[10px] uppercase text-slate-500">Jawaban dari {{ $game->buzzerGroup->name }}
                        </div>
                        <div class="text-lg text-white font-mono">{{ $game->currentAnswer->answer_text }}</div>
                    </div>
                @endif
                <div class="text-xs text-slate-400 mt-3">Hasil dalam <span class="font-display text-[#ffcc33] text-xl"
                        x-text="countdown">5</span> detik...</div>
            </div>
        @elseif ($game->session_status === 'result')
            <div class="text-center py-6">
                @if ($game->currentAnswer && $game->currentAnswer->is_correct)
                    <div class="font-display font-black text-4xl text-[#22e07b] mb-2">✓ BENAR!</div>
                    <div class="text-sm text-slate-300">+{{ $game->currentQuestion->points }} poin untuk
                        {{ $game->buzzerGroup->name }}</div>
                @else
                    <div class="font-display font-black text-4xl text-[#ff4757] mb-2">✗ SALAH!</div>
                    <div class="text-sm text-slate-300">Jawaban benar: <span
                            class="text-[#22e07b] font-mono">{{ $game->currentQuestion->answer_key }}</span></div>
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

    {{-- Overlay notif BENAR/SALAH (sama seperti leader) --}}
    <div x-show="showOverlay" class="fixed inset-0 z-50 flex items-center justify-center pointer-events-none"
        x-transition.opacity>
        <template x-if="overlayType === 'correct'">
            <div class="text-center" style="animation: popIn 0.5s;">
                <div
                    class="text-[12rem] font-display font-black text-[#22e07b] text-glow-blue animate-bounce leading-none">
                    ✓</div>
                <div class="text-6xl font-display font-black text-[#22e07b] text-glow-blue mt-4">BENAR!</div>
                <div class="text-xl text-[#ffcc33] font-display mt-3">🎂🎉</div>
            </div>
        </template>
        <template x-if="overlayType === 'wrong'">
            <div class="text-center" style="animation: shake 0.5s;">
                <div class="text-[12rem] font-display font-black text-[#ff4757] text-glow-red leading-none">✗</div>
                <div class="text-6xl font-display font-black text-[#ff4757] text-glow-red mt-4">SALAH!</div>
            </div>
        </template>
    </div>

    {{-- Confetti container --}}
    <div id="confetti" class="fixed inset-0 z-40 pointer-events-none"></div>

    @push('scripts')
        <script>
            function hostEffects() {
                return {
                    showOverlay: false,
                    overlayType: '',
                    init() {},
                    handleJudged(correct) {
                        this.overlayType = correct ? 'correct' : 'wrong';
                        this.showOverlay = true;

                        if (correct) this.playBirthdayFanfare();
                        else this.playWrongHeboh();

                        if (correct) this.fireConfetti();

                        setTimeout(() => this.showOverlay = false, 4000);
                    },
                    playBirthdayFanfare() {
                        const ctx = new(window.AudioContext || window.webkitAudioContext)();
                        const now = ctx.currentTime;

                        // === 1. FANFARE TRUMPET (do-mi-sol-do) ===
                        const notes = [523.25, 659.25, 783.99, 1046.50];
                        for (let round = 0; round < 2; round++) {
                            notes.forEach((freq, i) => {
                                const startTime = now + round * 0.5 + i * 0.1;
                                // 3 trumpet layer (sawtooth + slight detune)
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

                        // === 2. PARTY HORN / TEROMPET ULANG TAHUN (noise sweep) ===
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

                        // === 3. WHISTLES (cheer effect) ===
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

                        // === "Wah wah wah" trombone effect ===
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

                        // === Buzzer bass ===
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

                        // === "TETTTT" alarm beep ===
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
                    },
                    fireConfetti() {
                        const colors = ['#ffcc33', '#ff4757', '#38bdf8', '#22e07b', '#a855f7', '#ff9d2e'];
                        const container = document.getElementById('confetti');
                        for (let i = 0; i < 150; i++) {
                            const c = document.createElement('div');
                            const size = 6 + Math.random() * 14;
                            c.style.cssText = `
                        position: absolute;
                        left: ${Math.random() * 100}%;
                        top: -20px;
                        width: ${size}px;
                        height: ${size * 1.5}px;
                        background: ${colors[Math.floor(Math.random() * colors.length)]};
                        opacity: 0.9;
                        transform: rotate(${Math.random() * 360}deg);
                        animation: confetti-fall ${1.5 + Math.random() * 1.5}s linear forwards;
                        border-radius: 2px;
                    `;
                            container.appendChild(c);
                            setTimeout(() => c.remove(), 3500);
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
