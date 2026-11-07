<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SiswaController extends Controller
{
    /**
     * Ambil siswa yang sedang login dari guard 'siswa'.
     */
    private function student()
    {
        return Auth::guard('siswa')->user();
    }

    public function dashboard(): View
    {
        $student = $this->student();

        $statistik = [
            'hadir' => $student->absensi()->where('status', Absensi::STATUS_HADIR)->count(),
            'izin'  => $student->absensi()->where('status', Absensi::STATUS_IZIN)->count(),
        ];

        $absenHariIni = Absensi::where('student_id', $student->id)
            ->whereDate('tanggal', today())
            ->first();

        $riwayat = $student->absensi()
            ->latest('tanggal')
            ->limit(10)
            ->get();

        // ⚠️ PENTING: compact('student') wajib ada!
        return view('siswa.dashboard', compact('statistik', 'absenHariIni', 'riwayat', 'student'));
    }

    public function qr(): View
    {
        $student = $this->student();
        return view('siswa.qr', compact('student'));
    }

    public function riwayat(): View
    {
        $absensis = $this->student()->absensi()
            ->orderByDesc('tanggal')
            ->paginate(20);

        return view('siswa.riwayat', compact('absensis'));
    }
}
