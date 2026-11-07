<?php

namespace App\Models;

use App\Models\Absensi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'kelas',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public const ROLE_ADMIN = 'admin';
    public const ROLE_SISWA = 'siswa';

    public const ROLES = [
        self::ROLE_ADMIN => 'Admin',
        self::ROLE_SISWA => 'Siswa',
    ];

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }
    public function isSiswa(): bool
    {
        return $this->role === self::ROLE_SISWA;
    }
    public function isAktif(): bool
    {
        return (bool) $this->is_active;
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'user_id');
    }
}
