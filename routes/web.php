<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

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
    });

// ===== SISWA (guard siswa + role siswa) =====
Route::middleware(['auth:siswa', 'role:siswa'])
    ->prefix('siswa')
    ->name('siswa.')
    ->group(function () {
        Route::get('/dashboard', [SiswaController::class, 'dashboard'])->name('dashboard');
        Route::get('/qr',        [SiswaController::class, 'qr'])->name('qr');
        Route::get('/riwayat',   [SiswaController::class, 'riwayat'])->name('riwayat');
    });
