@extends('layouts.app')

@section('title', '403 - Akses Ditolak')

@section('layout-body')
    <main class="flex-1 flex items-center justify-center p-6">
        <div class="coc-panel coc-corner p-8 sm:p-12 text-center max-w-lg">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-[#ff4757] to-[#b91c1c] mb-4 shadow-[0_0_30px_rgba(255,71,87,0.5)]">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
            </div>
            <div class="font-display font-black text-5xl text-[#ff4757] text-glow-red">403</div>
            <h1 class="font-display font-bold text-xl text-white mt-2">AKSES DITOLAK</h1>
            <p class="text-sm text-slate-400 mt-2">{{ $exception->getMessage() ?: 'Anda tidak punya izin untuk mengakses halaman ini.' }}</p>

            @if (auth()->check())
                <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('siswa.dashboard') }}" class="btn-coc btn-gold mt-6">
                    Kembali ke Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-coc btn-gold mt-6">Ke Halaman Login</a>
            @endif
        </div>
    </main>
@endsection
