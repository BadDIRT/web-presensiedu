<div @scan-success.window="window.cocToast && window.cocToast($event.detail.message, 'success')"
    @scan-error.window="window.cocToast && window.cocToast($event.detail.message, 'error')">
    {{-- Toolbar --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <div>
            <span class="title-banner text-sm">TEMPLATE SOAL</span>
            <p class="text-xs text-slate-400 mt-2">
                Buat template soal untuk dipakai ulang saat membuat game baru.
            </p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="🔍 Cari template..."
                class="coc-input !py-2 !w-auto min-w-[200px]">
            <button type="button" wire:click="openCreateModal" class="btn-coc btn-gold">
                + Buat Template
            </button>
        </div>
    </div>

    {{-- Tabel template --}}
    <div class="coc-panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr
                        class="text-[10px] uppercase tracking-widest text-slate-400 border-b border-[var(--color-coc-border)]">
                        <th class="text-left px-4 py-3 font-semibold">#</th>
                        <th class="text-left px-4 py-3 font-semibold">Nama Template</th>
                        <th class="text-left px-4 py-3 font-semibold">Deskripsi</th>
                        <th class="text-left px-4 py-3 font-semibold">Jumlah Soal</th>
                        <th class="text-left px-4 py-3 font-semibold">Dibuat Oleh</th>
                        <th class="text-right px-4 py-3 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($templates as $i => $t)
                        <tr class="border-b border-[rgba(42,55,102,0.4)] hover:bg-white/[0.03]"
                            wire:key="tpl-{{ $t->id }}">
                            <td class="px-4 py-2.5 text-slate-500 text-xs">
                                {{ ($templates->currentPage() - 1) * $templates->perPage() + $i + 1 }}</td>
                            <td class="px-4 py-2.5 font-semibold text-white">{{ $t->name }}</td>
                            <td class="px-4 py-2.5 text-slate-300 text-xs">{{ $t->description ?? '-' }}</td>
                            <td class="px-4 py-2.5 text-slate-300">{{ $t->items_count }} soal</td>
                            <td class="px-4 py-2.5 text-slate-400 text-xs">{{ $t->creator?->name ?? '-' }}</td>
                            <td class="px-4 py-2.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="openEditModal({{ $t->id }})"
                                        class="btn-ghost btn-coc !p-2" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button wire:click="delete({{ $t->id }})"
                                        wire:confirm="Hapus template {{ $t->name }}?"
                                        class="btn-ghost btn-coc !p-2 hover:!text-[#ff4757]" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-slate-500">
                                <div class="text-sm">Belum ada template. Klik "Buat Template" untuk menambah.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($templates->hasPages())
            <div class="px-4 py-3 border-t border-[var(--color-coc-border)]">
                {{ $templates->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Form Create/Edit --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: true }" x-show="show" x-transition.opacity>
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="closeModal"></div>

            {{-- Modal container (bisa di-scroll) --}}
            <div class="relative min-h-full flex items-start justify-center p-4 sm:p-8">
                <div class="relative coc-panel p-6 w-full max-w-4xl my-4" x-show="show" x-transition.scale.origin>
                    {{-- Header sticky --}}
                    <div class="flex items-center justify-between mb-4 sticky top-0 bg-[#131a36] py-2 z-10 -mx-6 px-6">
                        <span class="title-banner text-sm">
                            {{ $editId ? 'EDIT TEMPLATE' : 'BUAT TEMPLATE BARU' }}
                        </span>
                        <button wire:click="closeModal" class="text-slate-400 hover:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form wire:submit="save" class="space-y-4">
                        {{-- Nama & Deskripsi --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label
                                    class="block text-[10px] uppercase tracking-widest text-slate-400 font-semibold mb-1.5">
                                    Nama Template <span class="text-[#ff4757]">*</span>
                                </label>
                                <input type="text" wire:model="name" class="coc-input"
                                    placeholder="Misal: Soal Matematika Dasar" required>
                                @error('name')
                                    <div class="text-[#ff8a93] text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] uppercase tracking-widest text-slate-400 font-semibold mb-1.5">
                                    Deskripsi (opsional)
                                </label>
                                <input type="text" wire:model="description" class="coc-input"
                                    placeholder="Keterangan singkat...">
                            </div>
                        </div>

                        {{-- Soal-soal --}}
                        <div class="text-[10px] uppercase tracking-widest text-slate-400 mb-2">Soal &amp; Kunci Jawaban
                            (3 fase × 10 soal)</div>

                        @foreach ($items as $i => $item)
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 coc-panel-flat p-3 mb-2">
                                <div class="sm:col-span-1 text-center font-display text-[#ffcc33] font-bold py-2">
                                    {{ $item['phase'] }}.{{ $item['order'] }}
                                </div>
                                <input type="text" wire:model="items.{{ $i }}.question_text"
                                    placeholder="Teks soal..." class="coc-input sm:col-span-7 !py-2">
                                <input type="text" wire:model="items.{{ $i }}.answer_key"
                                    placeholder="Kunci jawaban" class="coc-input sm:col-span-3 !py-2">
                                <input type="number" wire:model="items.{{ $i }}.points" min="1"
                                    max="100" class="coc-input sm:col-span-1 !py-2">
                            </div>
                        @endforeach

                        {{-- Aksi sticky di bawah --}}
                        <div class="flex gap-2 pt-2 sticky bottom-0 bg-[#131a36] py-3 -mx-6 px-6">
                            <button type="button" wire:click="closeModal"
                                class="btn-coc btn-ghost flex-1">Batal</button>
                            <button type="submit" class="btn-coc btn-gold flex-1">
                                {{ $editId ? 'Update Template' : 'Simpan Template' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
