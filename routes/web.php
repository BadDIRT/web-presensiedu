<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Livewire\GameHost;
use App\Livewire\GameManager;
use App\Livewire\GamePlayer;
use App\Livewire\GameLeaderboard;

Route::get('/', fn() => redirect()->route('login'))->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

// Logout (multi-guard aware)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ===== ADMIN (guard web + role admin) =====
Route::middleware(['auth:web', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/scan',      [AdminController::class, 'scan'])->name('scan');
        Route::get('/rekap',     [AdminController::class, 'rekap'])->name('rekap');

        // 🆕 CRUD Siswa
        Route::get('/students', [StudentController::class, 'index'])->name('students');

        // 🆕 Download kartu PNG per siswa
        Route::get('/students/{student}/card', [StudentController::class, 'downloadCardPng'])
            ->name('students.card');

        // Di dalam Route::group admin:
        Route::get('/games',                  [GameController::class, 'index'])->name('games.index');
        Route::get('/games/create',           [GameController::class, 'create'])->name('games.create');
        Route::post('/games',                 [GameController::class, 'store'])->name('games.store');
        Route::get('/games/{game}',           [GameController::class, 'show'])->name('games.show');
        Route::delete('/games/{game}',        [GameController::class, 'destroy'])->name('games.destroy');
        // Sesudah:
        Route::get('/games/{game}/host', [GameController::class, 'host'])->name('games.host');
        Route::get('/games/{game}/leaderboard', [GameController::class, 'leaderboard'])->name('games.leaderboard');
    });

// ===== SISWA (guard siswa + role siswa) =====
Route::middleware(['auth:siswa', 'role:siswa'])
    ->prefix('siswa')
    ->name('siswa.')
    ->group(function () {
        Route::get('/dashboard', [SiswaController::class, 'dashboard'])->name('dashboard');
        Route::get('/qr',        [SiswaController::class, 'qr'])->name('qr');
        Route::get('/riwayat',   [SiswaController::class, 'riwayat'])->name('riwayat');

        // 🆕 Game
        Route::get('/games', [GameController::class, 'siswaIndex'])->name('games.index');
        // Sesudah:
        Route::get('/games/{game}/play', [GameController::class, 'play'])->name('games.play');
        Route::get('/games/{game}/history', [GameController::class, 'siswaHistory'])->name('games.history');
    });
