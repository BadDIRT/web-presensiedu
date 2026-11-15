<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'created_by',
        'session_status',
        'current_question_id',
        'buzzer_group_id',
        'current_answer_id',
        'status_changed_at',
        'next_action_at',
        'current_phase',
        'current_q_order',
    ];

    protected $casts = [
        'status_changed_at' => 'datetime',
        'next_action_at'    => 'datetime',
    ];

    // Status game
    public const STATUS_DRAFT    = 'draft';
    public const STATUS_ACTIVE   = 'active';
    public const STATUS_FINISHED = 'finished';

    // Session state (real-time)
    public const SS_LOBBY              = 'lobby';
    public const SS_QUESTION_REVEALED  = 'question_revealed';
    public const SS_BUZZER_OPEN        = 'buzzer_open';
    public const SS_BUZZER_WON         = 'buzzer_won';
    public const SS_ANSWERING          = 'answering';
    public const SS_JUDGING            = 'judging';
    public const SS_RESULT             = 'result';
    public const SS_FINISHED           = 'finished';

    public function groups()
    {
        return $this->hasMany(GameGroup::class);
    }

    public function questions()
    {
        return $this->hasMany(GameQuestion::class)->orderBy('phase')->orderBy('order');
    }

    public function currentQuestion()
    {
        return $this->belongsTo(GameQuestion::class, 'current_question_id');
    }

    public function buzzerGroup()
    {
        return $this->belongsTo(GameGroup::class, 'buzzer_group_id');
    }

    public function currentAnswer()
    {
        return $this->belongsTo(GameAnswer::class, 'current_answer_id');
    }

    public function histories()
    {
        return $this->hasMany(GameHistory::class);
    }

    /**
     * Update session state dengan timestamp.
     */
    public function setSession(string $status, ?int $nextActionInSeconds = null): void
    {
        $this->session_status     = $status;
        $this->status_changed_at  = now();
        $this->next_action_at     = $nextActionInSeconds ? now()->addSeconds($nextActionInSeconds) : null;
        $this->save();
    }

    /**
     * Hitung jumlah detik sejak status berubah.
     */
    public function secondsSinceStatusChange(): int
    {
        return $this->status_changed_at ? $this->status_changed_at->diffInSeconds(now()) : 0;
    }

    /**
     * Hitung detik tersisa sampai next action.
     */
    public function secondsUntilNextAction(): int
    {
        if (! $this->next_action_at) return 0;
        return max(0, now()->diffInSeconds($this->next_action_at, false));
    }
}
