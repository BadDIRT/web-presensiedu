<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensis';

    protected $fillable = [
        'student_id',
        'tanggal',
        'jam_masuk',
        'status',
        'keterangan',
        'scanned_by',
    ];

    protected $casts = [
        'tanggal'   => 'date',
        'jam_masuk' => 'datetime:H:i:s',
    ];

    public const STATUS_HADIR = 'hadir';
    public const STATUS_IZIN  = 'izin';

    public const STATUSES = [
        self::STATUS_HADIR => 'Hadir',
        self::STATUS_IZIN  => 'Tidak Hadir/Izin',
    ];

    public const STATUS_COLORS = [
        self::STATUS_HADIR => 'badge-hadir',
        self::STATUS_IZIN  => 'badge-izin',
    ];

    // ⚠️ Relasi ke Student (WajIB ada!)
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    // Relasi ke User (admin yang scan)
    public function scanner()
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('tanggal', now()->toDateString());
    }

    public function scopeByDate($query, $tanggal)
    {
        return $query->whereDate('tanggal', $tanggal);
    }

    public function labelStatus(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function badgeClass(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'badge-izin';
    }
}
