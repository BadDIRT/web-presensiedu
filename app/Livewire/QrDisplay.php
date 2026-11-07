<?php

namespace App\Livewire;

use App\Models\Student;
use Livewire\Component;

class QrDisplay extends Component
{
    public int $studentId;
    public string $qrToken = '';

    public function mount(int $studentId): void
    {
        $this->studentId = $studentId;
        $this->qrToken = $this->generatePermanentToken();
    }

    public function generatePermanentToken(): string
    {
        $student = Student::find($this->studentId);
        if (! $student) {
            return '';
        }

        $payload = [
            'sid'  => $student->id,           // student id internal
            'id'   => $student->id_siswa,      // 6 digit ID untuk display
            'name' => $student->name,
            'sig'  => hash_hmac('sha256', $student->id . '|' . $student->id_siswa, config('app.key')),
        ];

        return base64_encode(json_encode($payload));
    }

    public function render()
    {
        return view('livewire.qr-display');
    }
}
