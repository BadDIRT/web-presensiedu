<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameGroup extends Model
{
    protected $fillable = ['game_id', 'name', 'leader_id', 'color', 'score'];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function leader()
    {
        return $this->belongsTo(Student::class, 'leader_id');
    }

    public function answers()
    {
        return $this->hasMany(GameAnswer::class, 'group_id');
    }
}
