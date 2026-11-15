@extends('layouts.app')

@section('title', 'Play Game')

@section('layout-body')
    @include('partials.sidebar-siswa')

    <main class="flex-1 p-4 sm:p-6 lg:p-8">
        <livewire:game-player :game="$game" />
    </main>
@endsection
