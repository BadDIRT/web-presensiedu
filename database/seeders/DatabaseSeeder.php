<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin utama (di table users)
        User::create([
            'name'      => 'Admin Sekolah',
            'email'     => 'admin@presensiedu.test',
            'password'  => 'admin12345',
            'role'      => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        // 15 siswa contoh di table students
        $namaContoh = [
            'Andi Pratama',
            'Budi Santoso',
            'Citra Lestari',
            'Dewi Anjani',
            'Eka Nugroho',
            'Fajar Ramadhan',
            'Gita Saraswati',
            'Hadi Wijaya',
            'Indah Permata',
            'Joko Susanto',
            'Kartika Dewi',
            'Lukman Hakim',
            'Maya Sari',
            'Nadia Putri',
            'Oka Pradana',
        ];

        for ($i = 0; $i < 15; $i++) {
            $idSiswa = str_pad((string)(100001 + $i), 6, '0', STR_PAD_LEFT);
            Student::create([
                'id_siswa'  => $idSiswa,
                'name'      => $namaContoh[$i],
                'kelas'     => 'Jarimath',
                'password'  => 'siswa12345',
                'is_active' => true,
            ]);
        }

        // Sample absensi hari ini
        $students = Student::limit(5)->get();
        $admin    = User::where('role', User::ROLE_ADMIN)->first();
        foreach ($students as $i => $s) {
            $jam = Carbon::today()->setTime(8 + $i, 5 + $i * 3);
            Absensi::create([
                'student_id' => $s->id,
                'tanggal'    => today(),
                'jam_masuk'  => $jam,
                'status'     => Absensi::STATUS_HADIR,
                'scanned_by' => $admin->id,
                'keterangan' => null,
            ]);
        }

        // 2 siswa izin
        $izinStudents = Student::whereNotIn('id', $students->pluck('id'))->limit(2)->get();
        foreach ($izinStudents as $s) {
            Absensi::create([
                'student_id' => $s->id,
                'tanggal'    => today(),
                'jam_masuk'  => null,
                'status'     => Absensi::STATUS_IZIN,
                'scanned_by' => $admin->id,
                'keterangan' => 'Izin orang tua',
            ]);
        }

        $this->command->info('Seed selesai.');
        $this->command->info('Admin  -> email: admin@presensiedu.test / pass: admin12345');
        $this->command->info('Siswa  -> ID: 100001..100015 / pass: siswa12345');
    }
}
