<?php

namespace App\Livewire;

use App\Models\Absensi;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;

/**
 * 🆕 Fitur: Salin Info Absensi
 *
 * Menghasilkan teks ringkasan absensi harian
 * siap paste ke grup WhatsApp orang tua siswa.
 */
class AttendanceInfo extends Component
{
    public string $tanggal = '';

    public function mount(?string $tanggal = null): void
    {
        $this->tanggal = $tanggal ?? now()->toDateString();
    }

    public function copyToClipboard(): void
    {
        $text = $this->generateText();
        $this->dispatch('copy-attendance-info', text: $text);
    }

    public function markRemainingAsIzin(): void
    {
        $absensis = Absensi::whereDate('tanggal', $this->tanggal)->get();
        $sudahAbsenIds = $absensis->pluck('student_id')->toArray();

        $belumAbsen = Student::where('is_active', true)
            ->whereNotIn('id', $sudahAbsenIds)
            ->get();

        if ($belumAbsen->isEmpty()) {
            $this->dispatch('scan-error', message: 'Semua siswa sudah tercatat absensinya.');
            return;
        }

        foreach ($belumAbsen as $siswa) {
            Absensi::create([
                'student_id' => $siswa->id,
                'tanggal'    => $this->tanggal,
                'jam_masuk'  => null,
                'status'     => Absensi::STATUS_IZIN,
                'scanned_by' => auth()->id(),
                'keterangan' => 'Tidak scan QR',
            ]);
        }

        $this->dispatch('attendance-added', userId: 0, status: 'izin');
        $this->dispatch(
            'scan-success',
            message: "{$belumAbsen->count()} siswa ditandai Tidak Hadir/Izin."
        );
    }

    public function generateText(): string
    {
        $date     = Carbon::parse($this->tanggal);
        $absensis = Absensi::with('student')
            ->whereDate('tanggal', $this->tanggal)
            ->whereNotNull('student_id')
            ->orderBy('jam_masuk')
            ->get();

        $totalSiswa = Student::where('is_active', true)->count();

        $hadirList = [];
        $izinList  = [];

        foreach ($absensis as $a) {
            if (! $a->student) continue;
            $name = $a->student->name;
            if ($a->status === Absensi::STATUS_HADIR) {
                if ($a->jam_masuk) {
                    $name .= ' (' . $a->jam_masuk->format('H:i') . ')';
                }
                $hadirList[] = $name;
            } else {
                $izinList[] = $a->student->name;
            }
        }

        $hadirCount = count($hadirList);
        $izinCount  = count($izinList);

        // Siswa yang belum ada record absensi sama sekali → gabung ke "izin"
        $sudahAbsenIds = $absensis->pluck('student_id')->toArray();
        $belumAbsen = Student::where('is_active', true)
            ->whereNotIn('id', $sudahAbsenIds)
            ->orderBy('name')
            ->pluck('name')
            ->toArray();
        $izinList  = array_merge($izinList, $belumAbsen);
        $izinCount = count($izinList);

        $lines = [];
        $lines[] = "📋 *LAPORAN ABSENSI HARIAN* 📋";
        $lines[] = "🏫 PresensiEdu — Clash of Champions";
        $lines[] = "📅 " . $date->translatedFormat('l, d F Y');
        $lines[] = "";
        $lines[] = "👥 *Total Siswa Aktif:* {$totalSiswa}";
        $lines[] = "";
        $lines[] = "📊 *RINGKASAN:*";
        $lines[] = "✅ Hadir: {$hadirCount}";
        $lines[] = "❌ Tidak Hadir/Izin: {$izinCount}";

        if ($totalSiswa > 0) {
            $persen = round(($hadirCount / $totalSiswa) * 100, 1);
            $lines[] = "📈 Persentase Kehadiran: {$persen}%";
        }
        $lines[] = "";

        if (! empty($hadirList)) {
            $lines[] = "✅ *HADIR ({$hadirCount}):*";
            $lines[] = implode(', ', $hadirList);
            $lines[] = "";
        }
        if (! empty($izinList)) {
            $lines[] = "❌ *TIDAK HADIR/IZIN ({$izinCount}):*";
            $lines[] = implode(', ', $izinList);
            $lines[] = "";
        }

        $lines[] = "_Pesan otomatis dari sistem PresensiEdu_";

        return implode("\n", $lines);
    }

    public function render()
    {
        $infoText = $this->generateText();
        return view('livewire.attendance-info', ['infoText' => $infoText]);
    }
}
