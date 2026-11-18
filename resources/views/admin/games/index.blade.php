@extends('layouts.app')

@section('title', 'Game Kuis')

@section('layout-body')
    @include('partials.sidebar-admin')

    <main class="flex-1 p-4 sm:p-6 lg:p-8">
        <div class="coc-hero p-5 mb-6">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div>
                    <span class="title-banner text-sm">ARENA KUIS</span>
                    <h1 class="font-display font-black text-2xl sm:text-3xl mt-3 text-glow-gold">GAME KUIS CLASH</h1>
                    <p class="text-sm text-slate-300 mt-1">Buat &amp; kelola game kuis antar kelompok siswa.</p>
                </div>
                <a href="{{ route('admin.games.create') }}" class="btn-coc btn-gold">
                    + Buat Game Baru
                </a>
            </div>
        </div>

        {{-- Filter & Search --}}
        <form method="GET" action="{{ route('admin.games.index') }}" class="coc-panel p-4 mb-4">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label
                        class="block text-[10px] uppercase tracking-widest text-slate-400 font-semibold mb-1">Cari</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="🔍 Nama game..."
                        class="coc-input !py-2 !w-auto min-w-[200px]">
                </div>
                <div>
                    <label
                        class="block text-[10px] uppercase tracking-widest text-slate-400 font-semibold mb-1">Status</label>
                    <select name="status" class="coc-input !py-2 !w-auto">
                        <option value="">Semua</option>
                        <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="finished" {{ $status === 'finished' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
                <button type="submit" class="btn-coc btn-gold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Cari
                </button>
                <a href="{{ route('admin.games.index') }}" class="btn-coc btn-ghost">Reset</a>
            </div>
        </form>

        <div class="coc-panel overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr
                        class="text-[10px] uppercase tracking-widest text-slate-400 border-b border-[var(--color-coc-border)]">
                        <th class="text-left px-4 py-3">#</th>
                        <th class="text-left px-4 py-3">Nama Game</th>
                        <th class="text-left px-4 py-3">Kelompok</th>
                        <th class="text-left px-4 py-3">Soal</th>
                        <th class="text-left px-4 py-3">Status</th>
                        <th class="text-right px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($games as $i => $g)
                        <tr class="border-b border-[rgba(42,55,102,0.4)] hover:bg-white/[0.03]">
                            <td class="px-4 py-2.5 text-slate-500 text-xs">
                                {{ ($games->currentPage() - 1) * $games->perPage() + $i + 1 }}</td>
                            <td class="px-4 py-2.5 font-semibold text-white">{{ $g->name }}</td>
                            <td class="px-4 py-2.5 text-slate-300">{{ $g->groups_count }} kelompok</td>
                            <td class="px-4 py-2.5 text-slate-300">{{ $g->questions_count }} soal</td>
                            <td class="px-4 py-2.5">
                                @if ($g->status === 'draft')
                                    <span class="badge badge-izin">Draft</span>
                                @elseif ($g->status === 'active')
                                    <span class="badge badge-terlambat">Aktif</span>
                                @else
                                    <span class="badge badge-hadir">Selesai</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-right">
                                <a href="{{ route('admin.games.show', $g) }}"
                                    class="btn-coc btn-ghost !py-1.5 !px-3 text-xs">Lihat</a>
                                @if ($g->status === 'draft')
                                    <a href="{{ route('admin.games.host', $g) }}"
                                        class="btn-coc btn-gold !py-1.5 !px-3 text-xs">Mulai</a>
                                @elseif ($g->status === 'active')
                                    <a href="{{ route('admin.games.host', $g) }}"
                                        class="btn-coc btn-red !py-1.5 !px-3 text-xs">Lanjut</a>
                                @else
                                    <a href="{{ route('admin.games.leaderboard', $g) }}"
                                        class="btn-coc btn-blue !py-1.5 !px-3 text-xs">Hasil</a>
                                @endif
                                <form method="POST" action="{{ route('admin.games.destroy', $g) }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="btn-coc btn-ghost !py-1.5 !px-3 text-xs hover:!text-[#ff4757]"
                                        onclick="return confirm('Hapus game ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-slate-500">
                                {{ $search || $status ? 'Tidak ada game yang cocok dengan filter.' : 'Belum ada game. Klik "Buat Game Baru" untuk memulai.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($games->hasPages())
            <div class="mt-4">
                {{ $games->links() }}
            </div>
        @endif
    </main>
@endsection
