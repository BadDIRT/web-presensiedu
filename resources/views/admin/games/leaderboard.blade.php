@extends('layouts.app')

@section('title', 'Leaderboard')

@section('layout-body')
    @include('partials.sidebar-admin')

    <main class="flex-1 p-4 sm:p-6 lg:p-8">
        <livewire:game-leaderboard :game="$game" />
    </main>
@endsection
