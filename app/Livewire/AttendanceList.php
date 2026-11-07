<?php

namespace App\Livewire;

use App\Models\Absensi;
use Livewire\Attributes\On;
use Livewire\Component;

class AttendanceList extends Component
{
    public ?int $freshId = null;
    public int $totalToday = 0;
    public string $lastUpdate = '';

    #[On('attendance-added')]
    public function refreshList(int $userId = 0, string $status = ''): void
    {
        $fresh = Absensi::where('student_id', $userId)
            ->whereDate('tanggal', today())
            ->first();
        $this->freshId = $fresh?->id;
        $this->lastUpdate = now()->format('H:i:s');
    }

    public function render()
    {
        $absensis = Absensi::with(['student', 'scanner'])
            ->whereDate('tanggal', today())
            ->whereNotNull('student_id')
            ->orderByDesc('jam_masuk')
            ->limit(50)
            ->get();

        $this->totalToday = $absensis->count();
        $this->lastUpdate = $this->lastUpdate ?: now()->format('H:i:s');

        return view('livewire.attendance-list', ['absensis' => $absensis]);
    }
}
