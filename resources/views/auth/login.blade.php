@extends('layouts.auth')

@section('title', 'Masuk Arena')

@section('content')
    <div class="w-full max-w-md mx-auto">
        {{-- Champion banner --}}
        <div class="coc-hero px-6 py-5 mb-5 text-center relative overflow-hidden">
            <div class="absolute inset-0 -z-10 opacity-30"
                style="background: radial-gradient(circle at 50% 0%, rgba(255,204,51,0.5), transparent 60%);"></div>
            <div class="flex items-center justify-center gap-2 mb-1">
                <span class="text-[10px] uppercase tracking-[0.3em] text-[#ffcc33] font-semibold">⚔ Arena Battle ⚔</span>
            </div>
            <h1 class="font-display font-black text-2xl sm:text-3xl text-glow-gold leading-tight">
                PRESENSIEDU
            </h1>
            <p class="text-xs sm:text-sm text-slate-300 mt-1 uppercase tracking-widest">CLASH OF CHAMPIONS</p>
        </div>

        {{-- Login card --}}
        <div class="coc-panel coc-corner p-6 sm:p-7">
            <div class="mb-5">
                <span class="title-banner text-sm">MASUK ARENA</span>
                <p class="text-sm text-slate-400 mt-3">Silakan masuk dengan ID &amp; password Anda.</p>
            </div>

            @if (session('error'))
                <div
                    class="mb-4 px-4 py-2.5 rounded-lg bg-[rgba(255,71,87,0.12)] border border-[rgba(255,71,87,0.5)] text-[#ff8a93] text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div
                    class="mb-4 px-4 py-2.5 rounded-lg bg-[rgba(255,71,87,0.12)] border border-[rgba(255,71,87,0.5)] text-[#ff8a93] text-sm">
                    @foreach ($errors->all() as $err)
                        <div class="flex items-start gap-2">
                            <span>⚠</span><span>{{ $err }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf

                {{-- ID Login --}}
                <div>
                    <label class="block text-xs uppercase tracking-widest text-slate-400 font-semibold mb-1.5">
                        ID Login <span class="text-[#ff4757]">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="login_id" id="login_id" value="{{ old('login_id') }}" required
                            autofocus placeholder="6 digit ID Siswa / email admin" pattern="[A-Za-z0-9@.]{3,50}"
                            title="6 digit ID untuk siswa, atau email admin"
                            class="coc-input pl-11 font-display tracking-wider" autocomplete="username">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1">Siswa: ID 6 digit · Admin: email</p>
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-xs uppercase tracking-widest text-slate-400 font-semibold mb-1.5">
                        Password <span class="text-[#ff4757]">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required minlength="8"
                            placeholder="Min. 8 karakter" class="coc-input pl-11 pr-11" autocomplete="current-password">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                        </span>
                        <button type="button"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-[#ffcc33]"
                            onclick="togglePass()">
                            <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                </path>
                            </svg>
                        </button>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1">Minimal 8 karakter</p>
                </div>

                {{-- Remember --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer select-none text-sm text-slate-300">
                        <input type="checkbox" name="remember" value="1"
                            class="rounded border-[var(--color-coc-border)] bg-[rgba(7,9,26,0.7)] text-[#ffcc33] focus:ring-[#ffcc33]">
                        Ingat saya
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-coc btn-gold w-full text-base !py-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                    MASUK ARENA
                </button>
            </form>

            {{-- Demo accounts --}}
            <div class="mt-6 pt-4 border-t border-[var(--color-coc-border)]">
                <div class="text-[10px] uppercase tracking-widest text-slate-500 mb-2">Akun Demo (seeder)</div>
                <div class="grid grid-cols-2 gap-2 text-[11px]">
                    <div class="coc-panel-flat p-2.5">
                        <div class="text-[#ffcc33] font-bold">⚔ Admin</div>
                        <div class="text-slate-300">admin@presensiedu.test</div>
                        <div class="text-slate-400">admin12345</div>
                    </div>
                    <div class="coc-panel-flat p-2.5">
                        <div class="text-[#38bdf8] font-bold">♟ Siswa</div>
                        <div class="text-slate-300 font-display tracking-wider">100001</div>
                        <div class="text-slate-400">siswa12345</div>
                    </div>
                </div>
            </div>
        </div>

        <p class="text-center text-[11px] text-slate-500 mt-4">
            &copy; {{ date('Y') }} PresensiEdu · Tampilan Clash of Champions
        </p>
    </div>

    <script>
        function togglePass() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML =
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
            } else {
                input.type = 'password';
                icon.innerHTML =
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
            }
        }

        // Auto-format: jika input ID adalah angka, batasi 6 digit
        document.getElementById('login_id').addEventListener('input', function(e) {
            const val = e.target.value.trim();
            // jika semua digit, batasi 6
            if (/^\d+$/.test(val) && val.length > 6) {
                e.target.value = val.slice(0, 6);
            }
        });
    </script>
@endsection
