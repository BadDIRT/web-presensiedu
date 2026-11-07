@extends('layouts.app')

@section('title', '404 - Halaman Tidak Ditemukan')

@section('layout-body')
    <main class="flex-1 flex items-center justify-center p-6">
        <div class="coc-panel coc-corner p-8 sm:p-12 text-center max-w-lg">
            <div class="font-display font-black text-6xl text-[#ffcc33] text-glow-gold">404</div>
            <h1 class="font-display font-bold text-xl text-white mt-2">HALAMAN TIDAK DITEMUKAN</h1>
            <p class="text-sm text-slate-400 mt-2">Arena yang kamu cari tidak ada di peta pertempuran.</p>

            <a href="{{ auth()->check() ? (auth()->user()->isAdmin() ? route('admin.dashboard') : route('siswa.dashboard')) : route('login') }}" class="btn-coc btn-gold mt-6">
                Kembali ke Base
            </a>
        </div>
    </main>
@endsection
