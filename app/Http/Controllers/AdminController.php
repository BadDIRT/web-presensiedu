<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $today = today();

        $statistik = [
            'total_siswa' => Student::where('is_active', true)->count(),
            'hadir'       => Absensi::whereDate('tanggal', $today)
                ->where('status', Absensi::STATUS_HADIR)
                ->whereNotNull('student_id')
                ->count(),
            'izin'        => Absensi::whereDate('tanggal', $today)
                ->where('status', Absensi::STATUS_IZIN)
                ->whereNotNull('student_id')
                ->count(),
        ];

        $recent = Absensi::with('student')
            ->whereDate('tanggal', $today)
            ->whereNotNull('student_id')
            ->latest()
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact('statistik', 'recent'));
    }

    public function scan(): View
    {
        return view('admin.scan');
    }

    public function rekap(Request $request): View
    {
        $tanggal = $request->input('tanggal', today()->toDateString());

        $absensis = Absensi::with(['student', 'scanner'])
            ->whereDate('tanggal', $tanggal)
            ->whereNotNull('student_id')
            ->orderByDesc('jam_masuk')
            ->get();

        $kelasList = Student::whereNotNull('kelas')
            ->where('kelas', '!=', '')
            ->distinct()
            ->pluck('kelas');

        return view('admin.rekap', compact('absensis', 'tanggal', 'kelasList'));
    }
}
