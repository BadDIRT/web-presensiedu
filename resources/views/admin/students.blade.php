@extends('layouts.app')

@section('title', 'Data Siswa')

@section('layout-body')
    @include('partials.sidebar-admin')

    <main class="flex-1 p-4 sm:p-6 lg:p-8">
        {{-- Hero --}}
        <div class="coc-hero p-5 mb-6 relative overflow-hidden">
            <div class="absolute inset-0 -z-10 opacity-25"
                style="background: radial-gradient(circle at 20% 0%, rgba(168,85,247,0.5), transparent 60%);"></div>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <span class="title-banner text-sm">ARENA PESERTA</span>
                    <h1 class="font-display font-black text-2xl sm:text-3xl mt-3 text-glow-gold">DATA SISWA</h1>
                    <p class="text-sm text-slate-300 mt-1">Kelola data siswa: tambah, edit, reset password, hapus.</p>
                </div>
                @include('partials.live-clock')
            </div>
        </div>

        <livewire:student-manager />

        @include('partials.alerts')
    </main>
@endsection
