<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * Halaman CRUD siswa (Livewire component akan handle logic).
     */
    public function index(): View
    {
        return view('admin.students');
    }
}
