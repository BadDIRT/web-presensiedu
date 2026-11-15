<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameAnswer extends Model
{
    protected $fillable = [
        'game_id',
        'group_id',
        'question_id',
        'answer_text',
        'is_correct',
        'buzzed_at',
        'answered_at',
        'judged_at',
    ];

    protected $casts = [
        'is_correct'  => 'boolean',
        'buzzed_at'   => 'datetime',
        'answered_at' => 'datetime',
        'judged_at'   => 'datetime',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function group()
    {
        return $this->belongsTo(GameGroup::class, 'group_id');
    }

    public function question()
    {
        return $this->belongsTo(GameQuestion::class, 'question_id');
    }
}
