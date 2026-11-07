<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'id_siswa',
        'name',
        'kelas',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relasi: 1 student punya banyak absensi.
     */
    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'student_id');
    }

    public function isAktif(): bool
    {
        return (bool) $this->is_active;
    }

    /**
     * Generate ID Siswa baru yang unik (6 digit, mulai dari 100001).
     */
    public static function generateUniqueStudentId(): string
    {
        do {
            $id = str_pad((string) random_int(100001, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('id_siswa', $id)->exists());

        return $id;
    }

    /**
     * Generate password acak 8 karakter (huruf+angka, tanpa karakter ambigu).
     */
    public static function generatePassword(int $length = 8): string
    {
        $chars = 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $out = '';
        for ($i = 0; $i < $length; $i++) {
            $out .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $out;
    }
}
