@extends('layouts.app')

@section('title', 'Buat Game')

@section('layout-body')
    @include('partials.sidebar-admin')

    <main class="flex-1 p-4 sm:p-6 lg:p-8">
        <div class="coc-hero p-5 mb-6">
            <span class="title-banner text-sm">BUAT GAME BARU</span>
            <h1 class="font-display font-black text-2xl mt-3 text-glow-gold">KONFIGURASI GAME</h1>
            <p class="text-sm text-slate-300 mt-1">Atur kelompok + 30 soal (3 fase × 10 soal).</p>
        </div>

        {{-- 🆕 Pilihan pakai template --}}
        @if ($templates->isNotEmpty())
            <div class="coc-panel p-5 mb-4" x-data="{ showTemplate: {{ $selectedTemplate ? 'true' : 'false' }} }">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <div class="text-[10px] uppercase tracking-widest text-slate-400">Pakai Template Soal?</div>
                        <div class="text-sm text-white">Pilih template untuk auto-fill 30 soal. Bisa di-edit setelahnya.
                        </div>
                    </div>
                    <button type="button" @click="showTemplate = !showTemplate"
                        class="btn-coc btn-ghost !py-2 !px-3 text-xs">
                        <span x-text="showTemplate ? 'Sembunyikan' : 'Tampilkan'"></span>
                    </button>
                </div>
                <div x-show="showTemplate" x-transition class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    @foreach ($templates as $t)
                        <a href="{{ route('admin.games.create', ['template' => $t->id]) }}"
                            class="coc-panel-flat p-3 hover:border-[#ffcc33] transition-all {{ $selectedTemplate == $t->id ? 'border-[#ffcc33] border-2' : '' }}">
                            <div class="font-semibold text-white text-sm">{{ $t->name }}</div>
                            <div class="text-xs text-slate-400">{{ $t->items_count }} soal</div>
                            @if ($selectedTemplate == $t->id)
                                <div class="text-[10px] text-[#22e07b] mt-1">✓ Dipilih</div>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.games.store') }}" class="space-y-6">
            @csrf

            {{-- Nama Game --}}
            <div class="coc-panel p-5">
                <div class="text-[10px] uppercase tracking-widest text-slate-400 mb-2">Nama Game</div>
                <input type="text" name="name" required class="coc-input" placeholder="Misal: Kuis Jarimath Bab 1">
            </div>

            {{-- Kelompok --}}
            <div class="coc-panel p-5" x-data="{ count: 2 }">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <div class="text-[10px] uppercase tracking-widest text-slate-400">Kelompok</div>
                        <div class="text-sm text-white">Buat minimal 2 kelompok. Setiap kelompok punya 1 leader.</div>
                    </div>
                    <button type="button" @click="count++" class="btn-coc btn-blue !py-2 !px-3 text-xs">+ Tambah
                        Kelompok</button>
                </div>
                <div class="space-y-3">
                    <template x-for="(g, i) in count" :key="i">
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 coc-panel-flat p-3">
                            <input type="text" :name="`groups[${i}][name]`" placeholder="Nama kelompok (A/B/C...)"
                                required class="coc-input sm:col-span-3 !py-2">
                            <select :name="`groups[${i}][leader_id]`" required class="coc-input sm:col-span-7 !py-2">
                                <option value="">— Pilih Leader —</option>
                                @foreach ($students as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->id_siswa }})</option>
                                @endforeach
                            </select>
                            <input type="color" :name="`groups[${i}][color]`" value="#ffcc33"
                                class="coc-input sm:col-span-2 !py-2 !px-2 !h-10">
                        </div>
                    </template>
                </div>
            </div>

            {{-- Soal-soal --}}
            <div class="coc-panel p-5" x-data="{ phases: 3, perPhase: 10 }">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <div class="text-[10px] uppercase tracking-widest text-slate-400">Soal &amp; Kunci Jawaban</div>
                        <div class="text-sm text-white">3 fase × 10 soal = 30 soal total.</div>
                    </div>
                    @if ($selectedTemplate)
                        <div class="text-xs text-[#22e07b]">✓ Auto-fill dari template</div>
                    @endif
                </div>

                @php
                    $templateItems = [];
                    if ($selectedTemplate) {
                        $templateItems =
                            \App\Models\QuestionTemplate::find($selectedTemplate)
                                ?->items->keyBy(function ($it) {
                                    return $it->phase . '_' . $it->order;
                                })
                                ->toArray() ?? [];
                    }
                @endphp

                @foreach (range(1, 3) as $p)
                    <div class="mb-6">
                        <div class="font-display font-bold text-[#ffcc33] mb-2">FASE {{ $p }}</div>
                        <div class="space-y-3">
                            @foreach (range(1, 10) as $o)
                                @php
                                    $idx = ($p - 1) * 10 + ($o - 1);
                                    $key = $p . '_' . $o;
                                    $prefillQ = $templateItems[$key]['question_text'] ?? '';
                                    $prefillA = $templateItems[$key]['answer_key'] ?? '';
                                    $prefillP =
                                        $templateItems[$key]['points'] ?? ($p === 1 ? 10 : ($p === 2 ? 15 : 20));
                                @endphp
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 coc-panel-flat p-3">
                                    <div class="sm:col-span-1 text-center font-display text-[#ffcc33] font-bold py-2">
                                        {{ $p }}.{{ $o }}
                                    </div>
                                    <input type="hidden" name="questions[{{ $idx }}][phase]"
                                        value="{{ $p }}">
                                    <input type="hidden" name="questions[{{ $idx }}][order]"
                                        value="{{ $o }}">
                                    <input type="text" name="questions[{{ $idx }}][question_text]"
                                        placeholder="Teks soal..." required value="{{ $prefillQ }}"
                                        class="coc-input sm:col-span-7 !py-2">
                                    <input type="text" name="questions[{{ $idx }}][answer_key]"
                                        placeholder="Kunci jawaban" required value="{{ $prefillA }}"
                                        class="coc-input sm:col-span-3 !py-2">
                                    <input type="number" name="questions[{{ $idx }}][points]"
                                        value="{{ $prefillP }}" min="1" max="100"
                                        class="coc-input sm:col-span-1 !py-2">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn-coc btn-gold w-full">Simpan &amp; Siap Dimainkan</button>
        </form>
    </main>
@endsection
