@extends('layouts.app')

@section('title', '500 - Server Error')

@section('layout-body')
    <main class="flex-1 flex items-center justify-center p-6">
        <div class="coc-panel coc-corner p-8 sm:p-12 text-center max-w-lg">
            <div class="font-display font-black text-6xl text-[#ff4757] text-glow-red">500</div>
            <h1 class="font-display font-bold text-xl text-white mt-2">SERVER ERROR</h1>
            <p class="text-sm text-slate-400 mt-2">Terjadi kesalahan di arena. Coba lagi sebentar.</p>

            <a href="javascript:history.back()" class="btn-coc btn-gold mt-6">Kembali</a>
        </div>
    </main>
@endsection
