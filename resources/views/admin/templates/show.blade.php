@extends('layouts.app')

@section('title', $template->name)

@section('layout-body')
    @include('partials.sidebar-admin')

    <main class="flex-1 p-4 sm:p-6 lg:p-8">
        <div class="coc-hero p-5 mb-6">
            <span class="title-banner text-sm">{{ strtoupper($template->name) }}</span>
            <h1 class="font-display font-black text-2xl mt-3 text-glow-gold">DETAIL TEMPLATE</h1>
            @if ($template->description)
                <p class="text-sm text-slate-300 mt-1">{{ $template->description }}</p>
            @endif
        </div>

        <div class="coc-panel p-5">
            @foreach ($template->items->groupBy('phase') as $phase => $items)
                <div class="mb-4">
                    <div class="font-display text-[#ffcc33] font-bold mb-2">FASE {{ $phase }}</div>
                    <div class="space-y-2">
                        @foreach ($items as $item)
                            <div class="coc-panel-flat p-3">
                                <div class="text-xs text-slate-400">{{ $phase }}.{{ $item->order }} ·
                                    {{ $item->points }} pts</div>
                                <div class="text-sm text-white">{{ $item->question_text }}</div>
                                <div class="text-xs text-[#22e07b] mt-1">Jawaban: {{ $item->answer_key }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            <a href="{{ route('admin.templates.index') }}" class="btn-coc btn-ghost">← Kembali</a>
        </div>
    </main>
@endsection
