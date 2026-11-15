<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameHistory extends Model
{
    protected $fillable = [
        'game_id',
        'game_name',
        'group_id',
        'student_id',
        'admin_id',
        'score',
        'rank',
        'role',
        'played_at',
    ];

    protected $casts = ['played_at' => 'datetime'];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
