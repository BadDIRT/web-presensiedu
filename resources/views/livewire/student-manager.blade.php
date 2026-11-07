<div @scan-success.window="window.cocToast && window.cocToast($event.detail.message, 'success')"
    @scan-error.window="window.cocToast && window.cocToast($event.detail.message, 'error')">
    {{-- Toolbar --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <div>
            <span class="title-banner text-sm">DATA SISWA</span>
            <p class="text-xs text-slate-400 mt-2">
                Kelola data siswa. Password dibuat otomatis saat tambah siswa baru.
            </p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="🔍 Cari nama / ID / kelas..."
                class="coc-input !py-2 !w-auto min-w-[200px]">
            <button type="button" wire:click="openCreateModal" class="btn-coc btn-gold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Siswa
            </button>
        </div>
    </div>

    {{-- Tabel siswa --}}
    <div class="coc-panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr
                        class="text-[10px] uppercase tracking-widest text-slate-400 border-b border-[var(--color-coc-border)]">
                        <th class="text-left px-4 py-3 font-semibold">#</th>
                        <th class="text-left px-4 py-3 font-semibold">ID Siswa</th>
                        <th class="text-left px-4 py-3 font-semibold">Nama</th>
                        <th class="text-left px-4 py-3 font-semibold">Kelas</th>
                        <th class="text-left px-4 py-3 font-semibold">Status</th>
                        <th class="text-right px-4 py-3 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $i => $s)
                        <tr class="border-b border-[rgba(42,55,102,0.4)] hover:bg-white/[0.03]"
                            wire:key="student-{{ $s->id }}">
                            <td class="px-4 py-2.5 text-slate-500 text-xs font-mono">
                                {{ ($students->currentPage() - 1) * $students->perPage() + $i + 1 }}</td>
                            <td class="px-4 py-2.5 font-display tracking-wider text-[#38bdf8]">{{ $s->id_siswa }}</td>
                            <td class="px-4 py-2.5 font-semibold text-white">{{ $s->name }}</td>
                            <td class="px-4 py-2.5 text-slate-300">{{ $s->kelas }}</td>
                            <td class="px-4 py-2.5">
                                @if ($s->is_active)
                                    <span class="badge badge-hadir">Aktif</span>
                                @else
                                    <span class="badge badge-alpa">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="openEditModal({{ $s->id }})"
                                        class="btn-ghost btn-coc !p-2" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button wire:click="regeneratePassword({{ $s->id }})"
                                        wire:confirm="Reset password {{ $s->name }}?"
                                        class="btn-ghost btn-coc !p-2" title="Reset Password">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                    </button>
                                    <button wire:click="toggleActive({{ $s->id }})"
                                        class="btn-ghost btn-coc !p-2"
                                        title="{{ $s->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        @if ($s->is_active)
                                            <svg class="w-4 h-4 text-[#ffcc33]" fill="none" stroke="currentColor"
                                                stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59" />
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-[#22e07b]" fill="none" stroke="currentColor"
                                                stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        @endif
                                    </button>
                                    <button wire:click="delete({{ $s->id }})"
                                        wire:confirm="Yakin hapus {{ $s->name }}? Data absensi terkait juga akan terhapus."
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
                                <div class="text-sm">Belum ada data siswa. Klik "Tambah Siswa" untuk menambah.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($students->hasPages())
            <div class="px-4 py-3 border-t border-[var(--color-coc-border)]">
                {{ $students->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Form Create/Edit --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data="{ show: true }" x-show="show"
            x-transition.opacity>
            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" wire:click="closeModal"></div>

            {{-- Modal panel --}}
            <div class="relative coc-panel p-6 w-full max-w-md" x-show="show" x-transition.scale.origin>
                <div class="flex items-center justify-between mb-4">
                    <span class="title-banner text-sm">
                        {{ $editId ? 'EDIT SISWA' : 'TAMBAH SISWA' }}
                    </span>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Notifikasi password baru --}}
                @if ($showPassword && $createdPassword)
                    <div class="mb-4 p-4 rounded-lg bg-[rgba(34,224,123,0.12)] border border-[rgba(34,224,123,0.5)]">
                        <div class="text-xs uppercase tracking-widest text-[#22e07b] font-bold mb-1">✅ Password
                            Otomatis Dibuat</div>
                        <div class="text-xs text-slate-300 mb-2">Catat password ini &amp; sampaikan ke siswa:</div>
                        <div class="flex items-center gap-2">
                            <code
                                class="flex-1 px-3 py-2 rounded bg-black/40 text-[#ffcc33] font-mono text-lg tracking-wider">
                                {{ $createdPassword }}
                            </code>
                            <button type="button" x-data="{ copied: false }"
                                @click="navigator.clipboard.writeText('{{ $createdPassword }}').then(() => { copied = true; setTimeout(() => copied = false, 2000); })"
                                class="btn-coc btn-blue !p-2" title="Salin password">
                                <span x-show="!copied">📋</span>
                                <span x-show="copied" class="text-[#22e07b]">✓</span>
                            </button>
                        </div>
                        <div class="mt-3 flex gap-2">
                            <button wire:click="closeModal" class="btn-coc btn-gold flex-1 text-xs">Selesai</button>
                        </div>
                    </div>
                @else
                    <form wire:submit="save" class="space-y-4">
                        {{-- Nama --}}
                        <div>
                            <label
                                class="block text-[10px] uppercase tracking-widest text-slate-400 font-semibold mb-1.5">
                                Nama Lengkap <span class="text-[#ff4757]">*</span>
                            </label>
                            <input type="text" wire:model="name" class="coc-input" placeholder="Nama siswa"
                                required autofocus>
                            @error('name')
                                <div class="text-[#ff8a93] text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ID Siswa --}}
                        <div>
                            <label
                                class="block text-[10px] uppercase tracking-widest text-slate-400 font-semibold mb-1.5">
                                ID Siswa (6 digit) <span class="text-[#ff4757]">*</span>
                            </label>
                            <div class="flex gap-2">
                                <input type="text" wire:model="id_siswa" maxlength="6" pattern="\d{6}"
                                    class="coc-input font-display tracking-wider text-[#ffcc33]" placeholder="100001"
                                    required>
                                @if (!$editId)
                                    <button type="button"
                                        wire:click="$set('id_siswa', '{{ \App\Models\Student::generateUniqueStudentId() }}')"
                                        class="btn-coc btn-ghost whitespace-nowrap" title="Generate ID baru">
                                        🎲
                                    </button>
                                @endif
                            </div>
                            @error('id_siswa')
                                <div class="text-[#ff8a93] text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Kelas --}}
                        <div>
                            <label
                                class="block text-[10px] uppercase tracking-widest text-slate-400 font-semibold mb-1.5">
                                Kelas
                            </label>
                            <input type="text" wire:model="kelas" class="coc-input" placeholder="Jarimath">
                            <div class="text-[11px] text-slate-500 mt-1">Default: Jarimath (boleh diubah)</div>
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="flex items-center gap-2 cursor-pointer text-sm text-slate-300">
                                <input type="checkbox" wire:model="is_active"
                                    class="rounded border-[var(--color-coc-border)] bg-[rgba(7,9,26,0.7)] text-[#ffcc33] focus:ring-[#ffcc33]">
                                Akun aktif (siswa bisa login)
                            </label>
                        </div>

                        {{-- Info password auto --}}
                        @if (!$editId)
                            <div
                                class="text-xs p-3 rounded-lg bg-[rgba(56,189,248,0.08)] border border-[rgba(56,189,248,0.3)] text-[#38bdf8]">
                                🔐 Password akan dibuat otomatis (8 karakter acak) &amp; ditampilkan setelah disimpan.
                            </div>
                        @endif

                        {{-- Aksi --}}
                        <div class="flex gap-2 pt-2">
                            <button type="button" wire:click="closeModal"
                                class="btn-coc btn-ghost flex-1">Batal</button>
                            <button type="submit" class="btn-coc btn-gold flex-1">
                                {{ $editId ? 'Update' : 'Simpan' }}
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    @endif
</div>
