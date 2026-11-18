<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\GameAnswer;
use App\Models\GameGroup;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class GamePlayer extends Component
{
    public Game $game;
    public ?int $myGroupId = null;
    public string $answerText = '';
    public ?string $previousStatus = null;

    public function mount(Game $game)
    {
        $this->game = $game;
        $student = Auth::guard('siswa')->user();
        $group = GameGroup::where('game_id', $game->id)
            ->where('leader_id', $student->id)
            ->first();
        $this->myGroupId = $group?->id;
        $this->previousStatus = $game->session_status;
    }

    public function refreshState(): void
    {
        $this->game->refresh();

        // Detect transisi judging → result → dispatch event ke browser
        $currentStatus = $this->game->session_status;
        if ($this->previousStatus === Game::SS_JUDGING && $currentStatus === Game::SS_RESULT) {
            $isCorrect = $this->game->currentAnswer?->is_correct ?? false;
            $this->dispatch('answer-judged', correct: $isCorrect);
        }
        $this->previousStatus = $currentStatus;
    }

    public function pressBuzzer(): void
    {
        if (! $this->myGroupId) return;
        if ($this->game->session_status !== Game::SS_BUZZER_OPEN) return;
        if ($this->game->buzzer_group_id) return;

        // Suara tekan buzzer
        $this->dispatch('buzzer-pressed');

        if (! $this->game->buzzer_group_id) {
            $this->game->buzzer_group_id = $this->myGroupId;
            $this->game->save();

            $answer = GameAnswer::create([
                'game_id'     => $this->game->id,
                'group_id'    => $this->myGroupId,
                'question_id' => $this->game->current_question_id,
                'buzzed_at'   => now(),
            ]);
            $this->game->current_answer_id = $answer->id;
            $this->game->save();
            $this->game->setSession(Game::SS_BUZZER_WON);
            $this->previousStatus = Game::SS_BUZZER_WON;
        }
    }

    /**
     * Submit jawaban → set status judging (delay 5 detik sebelum auto-judge).
     */
    public function submitAnswer(): void
    {
        if (! $this->answerText) return;
        if ($this->game->session_status !== Game::SS_BUZZER_WON) return;
        if ($this->game->buzzer_group_id !== $this->myGroupId) return;
        if (! $this->game->current_answer_id) return;

        $answer = GameAnswer::find($this->game->current_answer_id);
        $answer->answer_text = trim($this->answerText);
        $answer->answered_at = now();
        $answer->save();

        // Set judging dengan delay 5 detik (admin akan autoJudge via polling)
        $this->game->setSession(Game::SS_JUDGING, 5);
        $this->previousStatus = Game::SS_JUDGING;

        $this->answerText = '';
        $this->dispatch('answer-submitted');
    }

    public function render()
    {
        $game = $this->game->fresh(['groups.leader', 'currentQuestion', 'buzzerGroup', 'currentAnswer']);
        $myGroup = $this->myGroupId ? GameGroup::with('leader')->find($this->myGroupId) : null;
        return view('livewire.game-player', compact('game', 'myGroup'));
    }
}
