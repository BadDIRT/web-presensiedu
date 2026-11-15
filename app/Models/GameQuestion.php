<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameQuestion extends Model
{
    protected $fillable = ['game_id', 'phase', 'order', 'question_text', 'answer_key', 'points'];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function answers()
    {
        return $this->hasMany(GameAnswer::class, 'question_id');
    }
}
