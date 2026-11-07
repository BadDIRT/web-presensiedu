@php
    $admin = auth()->user();
@endphp

<div x-data="qrScannerComponent()" x-init="init()" @scan-success.window="handleSuccess($event)"
    @scan-error.window="handleError($event)" class="flex flex-col gap-4">
    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <span class="title-banner">SCAN QR SISWA</span>
            <p class="text-sm text-slate-400 mt-2 max-w-lg">
                Arahkan kamera ke QR code siswa. Sistem otomatis menyimpan absensi secara
                <span class="text-[#22e07b] font-semibold">real-time</span> tanpa refresh.
            </p>
        </div>
        <div class="flex items-center gap-2">
            {{-- Tombol Mulai Scan: tampil saat TIDAK scanning --}}
            <button type="button" @click="toggleCamera()" class="btn-coc btn-gold" x-show="!scanning">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Mulai Scan
            </button>
            {{-- Tombol Stop Scan: tampil saat scanning --}}
            <button type="button" @click="stopCamera()" class="btn-coc btn-red" x-show="scanning">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <rect x="6" y="6" width="12" height="12" rx="1" />
                </svg>
                Stop Scan
            </button>
            {{-- Tombol Ganti Kamera: tampil saat scanning --}}
            <button type="button" @click="switchCamera()" class="btn-coc btn-ghost" x-show="scanning"
                title="Ganti kamera">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Camera viewport --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2">
            <div class="scan-viewport relative">
                {{-- Video element --}}
                <video id="qr-video" playsinline muted></video>
                <canvas id="qr-canvas" class="hidden"></canvas>

                {{-- Overlay (target & laser) --}}
                <div class="scan-overlay" x-show="scanning">
                    <div class="scan-target"></div>
                    <div class="scan-laser"></div>
                </div>

                {{-- Idle state --}}
                <div x-show="!scanning"
                    class="absolute inset-0 flex flex-col items-center justify-center gap-3 text-slate-400">
                    <svg class="w-16 h-16 opacity-50" fill="none" stroke="currentColor" stroke-width="1.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <div class="text-center">
                        <div class="text-sm font-semibold text-slate-300">Kamera Belum Aktif</div>
                        <div class="text-xs mt-1">Klik tombol "Mulai Scan" untuk mulai memindai QR siswa.</div>
                    </div>
                </div>

                {{-- Permission denied --}}
                <div x-show="permissionDenied"
                    class="absolute inset-0 flex flex-col items-center justify-center gap-2 text-center px-6">
                    <svg class="w-12 h-12 text-[#ff4757]" fill="none" stroke="currentColor" stroke-width="1.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                    <div class="text-[#ff8a93] font-semibold text-sm">Akses kamera ditolak</div>
                    <div class="text-xs text-slate-400">Izinkan akses kamera di browser, lalu muat ulang halaman.</div>
                </div>

                {{-- Live badge --}}
                <div x-show="scanning"
                    class="absolute top-3 left-3 flex items-center gap-2 px-2.5 py-1 rounded-md bg-black/60 backdrop-blur">
                    <span class="live-dot"></span>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-[#ff4757]">LIVE</span>
                </div>
            </div>

            {{-- Manual input fallback --}}
            <div class="coc-panel-flat p-3 mt-3">
                <div class="text-[10px] uppercase tracking-widest text-slate-500 mb-1.5">Atau input manual token</div>
                <div class="flex gap-2">
                    <input type="text" x-model="manualToken" placeholder="Tempel token QR di sini..."
                        class="coc-input text-xs font-mono">
                    <button type="button" @click="submitManual()" class="btn-coc btn-blue whitespace-nowrap">
                        Proses
                    </button>
                </div>
            </div>
        </div>

        {{-- Side panel: feedback status --}}
        <div class="lg:col-span-1">
            <div class="coc-panel p-4 h-full">
                <div class="text-[10px] uppercase tracking-widest text-slate-500 mb-3">Status Scan Terakhir</div>

                <template x-if="!lastResult">
                    <div class="text-center py-8 text-slate-500">
                        <svg class="w-10 h-10 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor"
                            stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-xs">Belum ada scan</div>
                    </div>
                </template>

                <template x-if="lastResult">
                    <div class="space-y-3">
                        <div class="text-center py-3"
                            :class="lastResult.type === 'success' ? 'text-[#22e07b]' : (lastResult.type === 'duplicate' ?
                                'text-[#ffcc33]' : 'text-[#ff4757]')">
                            <svg class="w-12 h-12 mx-auto mb-1" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <template x-if="lastResult.type === 'success'">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </template>
                                <template x-if="lastResult.type === 'duplicate'">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </template>
                                <template x-if="lastResult.type === 'error'">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </template>
                            </svg>
                            <div class="font-display font-bold uppercase tracking-wider text-sm"
                                x-text="lastResult.type === 'success' ? 'Berhasil' : (lastResult.type === 'duplicate' ? 'Duplikat' : 'Gagal')">
                            </div>
                        </div>
                        <template x-if="lastResult.name">
                            <div class="coc-panel-flat p-3">
                                <div class="text-[10px] uppercase tracking-widest text-slate-400">Siswa</div>
                                <div class="font-semibold text-sm text-white" x-text="lastResult.name"></div>
                                <div class="text-xs text-[#ffcc33] font-display tracking-wider mt-0.5"
                                    x-text="lastResult.nis || ''"></div>
                            </div>
                        </template>
                        <div class="text-xs text-slate-300" x-text="lastResult.message"></div>
                    </div>
                </template>

                <div
                    class="mt-4 pt-3 border-t border-[var(--color-coc-border)] text-[10px] uppercase tracking-widest text-slate-500">
                    Scanner by: {{ $admin->name }}
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden form: Livewire event trigger --}}
    {{-- Setelah frontend baca QR via jsQR, akan dispatch 'qr-detected' ke Livewire via $wire --}}
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
    <script>
        function qrScannerComponent() {
            return {
                scanning: false,
                cameraReady: false,
                permissionDenied: false,
                stream: null,
                rafId: null,
                facingMode: 'environment',
                manualToken: '',
                lastResult: null,
                lastScanTime: 0,

                async init() {
                    // Hook ke event Livewire 'scan-success' dan 'scan-error'
                },

                async toggleCamera() {
                    if (this.scanning) return;
                    await this.startCamera();
                },

                async startCamera() {
                    try {
                        this.stream = await navigator.mediaDevices.getUserMedia({
                            video: {
                                facingMode: {
                                    ideal: this.facingMode
                                },
                                width: {
                                    ideal: 1280
                                },
                                height: {
                                    ideal: 720
                                }
                            },
                            audio: false
                        });
                        const video = document.getElementById('qr-video');
                        video.srcObject = this.stream;
                        await video.play();
                        this.scanning = true;
                        this.cameraReady = true;
                        this.permissionDenied = false;
                        this.tick();
                    } catch (err) {
                        console.error('Camera error:', err);
                        this.permissionDenied = true;
                        this.scanning = false;
                        window.cocToast && window.cocToast('Gagal mengakses kamera: ' + err.message, 'error');
                    }
                },

                stopCamera() {
                    this.scanning = false;
                    if (this.rafId) cancelAnimationFrame(this.rafId);
                    if (this.stream) {
                        this.stream.getTracks().forEach(t => t.stop());
                        this.stream = null;
                    }
                    const video = document.getElementById('qr-video');
                    if (video) video.srcObject = null;
                },

                async switchCamera() {
                    this.facingMode = this.facingMode === 'environment' ? 'user' : 'environment';
                    this.stopCamera();
                    await this.startCamera();
                },

                tick() {
                    if (!this.scanning) return;
                    const video = document.getElementById('qr-video');
                    const canvas = document.getElementById('qr-canvas');
                    if (!video || !canvas || video.readyState !== video.HAVE_ENOUGH_DATA) {
                        this.rafId = requestAnimationFrame(() => this.tick());
                        return;
                    }
                    const ctx = canvas.getContext('2d', {
                        willReadFrequently: true
                    });
                    const w = video.videoWidth || 640;
                    const h = video.videoHeight || 480;
                    canvas.width = w;
                    canvas.height = h;
                    ctx.drawImage(video, 0, 0, w, h);
                    try {
                        const imgData = ctx.getImageData(0, 0, w, h);
                        const code = (typeof jsQR !== 'undefined') ? jsQR(imgData.data, w, h, {
                            inversionAttempts: 'dontInvert'
                        }) : null;
                        if (code && code.data) {
                            const now = Date.now();
                            // throttle: min 1.5 detik antar scan sama
                            if (now - this.lastScanTime > 1500) {
                                this.lastScanTime = now;
                                this.dispatchScan(code.data);
                            }
                        }
                    } catch (e) {
                        // ignore frame error
                    }
                    this.rafId = requestAnimationFrame(() => this.tick());
                },

                dispatchScan(token) {
                    // Kirim ke Livewire via $wire
                    // @this sudah otomatis tersedia di dalam Livewire component
                    const wireEl = this.$el.closest('[wire\\:id]') || this.$el;
                    if (window.Livewire && wireEl.getAttribute('wire:id')) {
                        const comp = window.Livewire.find(wireEl.getAttribute('wire:id'));
                        if (comp) {
                            comp.call('onQrDetected', token);
                            return;
                        }
                    }
                    // Fallback: dispatch event global (akan didengar Livewire via #[On('qr-detected')])
                    window.dispatchEvent(new CustomEvent('qr-detected', {
                        detail: {
                            token
                        }
                    }));
                },

                submitManual() {
                    if (!this.manualToken || this.manualToken.length < 10) {
                        window.cocToast && window.cocToast('Token tidak valid', 'error');
                        return;
                    }
                    this.dispatchScan(this.manualToken.trim());
                    this.manualToken = '';
                },

                handleSuccess(e) {
                    const data = e.detail || {};
                    this.lastResult = {
                        type: 'success',
                        name: data.name || null,
                        nis: data.nis || null,
                        message: data.message || 'Absensi berhasil disimpan.'
                    };
                    window.cocToast && window.cocToast(data.message || 'Absensi berhasil', 'success');
                    // beep sukses
                    this.beep(880, 0.08);
                },

                handleError(e) {
                    const data = e.detail || {};
                    this.lastResult = {
                        type: data.type || 'error',
                        name: data.name || null,
                        nis: data.nis || null,
                        message: data.message || 'Terjadi kesalahan saat scan.'
                    };
                    window.cocToast && window.cocToast(data.message || 'Scan gagal', 'error');
                    this.beep(220, 0.15);
                },

                beep(freq, duration) {
                    try {
                        const ctx = new(window.AudioContext || window.webkitAudioContext)();
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.frequency.value = freq;
                        osc.type = 'square';
                        gain.gain.setValueAtTime(0.15, ctx.currentTime);
                        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + duration);
                        osc.start();
                        osc.stop(ctx.currentTime + duration);
                    } catch (e) {}
                }
            };
        }

        // Listen Livewire events dan trigger Alpine
        document.addEventListener('livewire:init', () => {
            Livewire.on('scan-success', (payload) => {
                const data = (Array.isArray(payload) ? payload[0] : payload) || {};
                window.dispatchEvent(new CustomEvent('scan-success', {
                    detail: data
                }));
            });
            Livewire.on('scan-error', (payload) => {
                const data = (Array.isArray(payload) ? payload[0] : payload) || {};
                // Determine type by message
                let type = 'error';
                if (data.message && data.message.toLowerCase().includes('sudah absen')) type = 'duplicate';
                window.dispatchEvent(new CustomEvent('scan-error', {
                    detail: {
                        ...data,
                        type
                    }
                }));
            });
        });

        // Cleanup saat keluar halaman
        window.addEventListener('beforeunload', () => {
            const video = document.getElementById('qr-video');
            if (video && video.srcObject) {
                video.srcObject.getTracks().forEach(t => t.stop());
            }
        });
    </script>
@endpush
