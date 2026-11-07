<?php

namespace App\Livewire;

use App\Models\Absensi;
use App\Models\Student;
use Livewire\Attributes\On;
use Livewire\Component;

class StatCards extends Component
{
    public int $totalSiswa = 0;
    public int $hadir = 0;
    public int $izin  = 0;

    #[On('attendance-added')]
    public function refreshStats(): void
    {
        $this->hydrateStats();
    }
    public function mount(): void
    {
        $this->hydrateStats();
    }

    private function hydrateStats(): void
    {
        $today = today();
        $this->totalSiswa = Student::where('is_active', true)->count();

        $this->hadir = Absensi::whereDate('tanggal', $today)
            ->where('status', Absensi::STATUS_HADIR)
            ->whereNotNull('student_id')
            ->count();

        $this->izin = Absensi::whereDate('tanggal', $today)
            ->where('status', Absensi::STATUS_IZIN)
            ->whereNotNull('student_id')
            ->count();
    }

    public function render()
    {
        return view('livewire.stat-cards');
    }
}
