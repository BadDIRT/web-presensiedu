<?php

namespace App\Livewire;

use App\Models\Absensi;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Livewire\Attributes\On;
use Livewire\Component;

class QrScanner extends Component
{
    public string $scannedToken = '';
    public ?string $lastStatus = null;
    public ?string $lastMessage = null;
    public ?string $lastSiswaName = null;
    public ?string $lastSiswaNis = null;

    #[On('qr-detected')]
    public function onQrDetected(string $token): void
    {
        $this->processScan($token);
    }

    public function processScan(string $token): void
    {
        $this->scannedToken = $token;

        // Decode token permanen (base64 JSON)
        $json    = base64_decode($token, true);
        $payload = $json ? json_decode($json, true) : null;

        if (! is_array($payload) || ! isset($payload['sid'], $payload['id'], $payload['sig'])) {
            $this->setErrorState('Token QR tidak valid', 'QR tidak dikenali.');
            $this->dispatch('scan-error', message: 'Token tidak valid');
            return;
        }

        // Verifikasi signature
        $expectedSig = hash_hmac('sha256', $payload['sid'] . '|' . $payload['id'], config('app.key'));
        if (! hash_equals($expectedSig, $payload['sig'])) {
            $this->setErrorState('Signature tidak valid', 'QR dipalsukan atau rusak.');
            $this->dispatch('scan-error', message: 'QR tidak valid');
            return;
        }

        // Cari student
        $student = \App\Models\Student::find((int) $payload['sid']);
        if (! $student) {
            $this->setErrorState('Siswa tidak ditemukan', 'User tidak terdaftar.');
            $this->dispatch('scan-error', message: 'Siswa tidak ditemukan');
            return;
        }
        if (! $student->is_active) {
            $this->setErrorState('Akun dinonaktifkan', $student->name . ' tidak aktif.');
            $this->dispatch('scan-error', message: 'Akun nonaktif');
            return;
        }

        // Cek duplikat hari ini
        $existing = \App\Models\Absensi::where('student_id', $student->id)
            ->whereDate('tanggal', today())
            ->first();

        if ($existing) {
            $this->lastStatus    = 'duplicate';
            $this->lastSiswaName = $student->name;
            $this->lastSiswaNis  = $student->id_siswa;
            $this->lastMessage   = "{$student->name} sudah absen pukul {$existing->jam_masuk?->format('H:i:s')}.";
            $this->dispatch(
                'scan-error',
                message: $this->lastMessage,
                name: $student->name,
                nis: $student->id_siswa,
                type: 'duplicate'
            );
            return;
        }

        // Simpan absensi
        $jamMasuk = \Illuminate\Support\Carbon::now();
        $status   = \App\Models\Absensi::STATUS_HADIR;

        \App\Models\Absensi::create([
            'student_id' => $student->id,
            'tanggal'    => today(),
            'jam_masuk'  => $jamMasuk,
            'status'     => $status,
            'scanned_by' => auth()->id(), // admin (user) id
            'keterangan' => null,
        ]);

        $this->lastStatus    = 'success';
        $this->lastSiswaName = $student->name;
        $this->lastSiswaNis  = $student->id_siswa;
        $this->lastMessage   = "{$student->name} berhasil absen (Hadir) pukul {$jamMasuk->format('H:i:s')}.";

        $this->dispatch('attendance-added', userId: $student->id, status: $status);
        $this->dispatch(
            'scan-success',
            message: $this->lastMessage,
            name: $student->name,
            nis: $student->id_siswa,
            type: 'success'
        );
    }

    public function clearStatus(): void
    {
        $this->reset(['lastStatus', 'lastMessage', 'lastSiswaName', 'lastSiswaNis']);
    }

    private function setErrorState(string $title, string $detail): void
    {
        $this->lastStatus    = 'error';
        $this->lastMessage   = $title . ' — ' . $detail;
        $this->lastSiswaName = null;
        $this->lastSiswaNis  = null;
    }

    public function render()
    {
        return view('livewire.qr-scanner');
    }
}
