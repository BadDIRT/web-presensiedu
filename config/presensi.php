<?php

return [
    'jam_mulai'    => env('PRESENSI_JAM_MULAI', '07:00'),
    'jam_akhir'    => env('PRESENSI_JAM_AKHIR', '08:00'),
    'terlambat'   => (int) env('PRESENSI_TERLAMBAT_MENIT', 15),
    'status' => [
        'hadir'      => 'Hadir',
        'terlambat'  => 'Terlambat',
        'izin'       => 'Izin',
        'sakit'      => 'Sakit',
        'alpa'       => 'Alpa',
    ],
];
