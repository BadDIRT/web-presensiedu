@extends('layouts.app')

@section('title', 'Template Soal')

@section('layout-body')
    @include('partials.sidebar-admin')

    <main class="flex-1 p-4 sm:p-6 lg:p-8">
        <div class="coc-hero p-5 mb-6 relative overflow-hidden">
            <div class="absolute inset-0 -z-10 opacity-25"
                style="background: radial-gradient(circle at 20% 0%, rgba(168,85,247,0.5), transparent 60%);"></div>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <span class="title-banner text-sm">TEMPLATE SOAL</span>
                    <h1 class="font-display font-black text-2xl sm:text-3xl mt-3 text-glow-gold">BANK SOAL REUSABLE</h1>
                    <p class="text-sm text-slate-300 mt-1">Buat sekali, pakai berkali-kali saat membuat game baru.</p>
                </div>
                @include('partials.live-clock')
            </div>
        </div>

        <livewire:question-template-manager />

        @include('partials.alerts')
    </main>
@endsection
