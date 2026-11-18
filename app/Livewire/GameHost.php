<?php

namespace App\Livewire;

use App\Models\Game;
use App\Models\GameAnswer;
use App\Models\GameGroup;
use App\Models\GameHistory;
use App\Models\GameQuestion;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class GameHost extends Component
{
    public Game $game;
    public ?string $previousStatus = null;

    public function mount(Game $game)
    {
        $this->game = $game;
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

        // Auto-advance kalau next_action_at sudah lewat
        if ($this->game->next_action_at && now()->gte($this->game->next_action_at)) {
            $this->autoAdvance();
        }
    }

    public function startGame(): void
    {
        if ($this->game->status !== Game::STATUS_DRAFT) return;
        $this->game->status = Game::STATUS_ACTIVE;
        $this->game->current_phase = 1;
        $this->game->current_q_order = 0;
        $this->game->save();
        $this->revealNextQuestion();
    }

    public function revealNextQuestion(): void
    {
        $nextOrder = $this->game->current_q_order + 1;
        $currentPhase = $this->game->current_phase;

        $q = GameQuestion::where('game_id', $this->game->id)
            ->where('phase', $currentPhase)
            ->where('order', $nextOrder)
            ->first();

        if (! $q) {
            if ($currentPhase < 3) {
                $this->game->current_phase = $currentPhase + 1;
                $this->game->current_q_order = 0;
                $q = GameQuestion::where('game_id', $this->game->id)
                    ->where('phase', $currentPhase + 1)
                    ->where('order', 1)
                    ->first();
            } else {
                $this->endGame();
                return;
            }
        }

        $this->game->current_q_order = $q->order;
        $this->game->current_question_id = $q->id;
        $this->game->buzzer_group_id = null;
        $this->game->current_answer_id = null;
        $this->game->save();
        $this->game->setSession(Game::SS_QUESTION_REVEALED);
        $this->previousStatus = Game::SS_QUESTION_REVEALED;
    }

    public function openBuzzer(): void
    {
        if ($this->game->session_status !== Game::SS_QUESTION_REVEALED) return;
        $this->game->setSession(Game::SS_BUZZER_OPEN);
        $this->previousStatus = Game::SS_BUZZER_OPEN;
    }

    public function buzz(int $groupId): void
    {
        if ($this->game->session_status !== Game::SS_BUZZER_OPEN) return;
        if ($this->game->buzzer_group_id) return;

        $this->game->buzzer_group_id = $groupId;
        $this->game->save();

        $answer = GameAnswer::create([
            'game_id'     => $this->game->id,
            'group_id'    => $groupId,
            'question_id' => $this->game->current_question_id,
            'buzzed_at'   => now(),
        ]);
        $this->game->current_answer_id = $answer->id;
        $this->game->save();
        $this->game->setSession(Game::SS_BUZZER_WON);
        $this->previousStatus = Game::SS_BUZZER_WON;
    }

    /**
     * Auto-judge setelah delay 5 detik (dipanggil dari polling saat next_action_at lewat).
     */
    public function autoJudge(): void
    {
        if (! $this->game->current_answer_id) {
            $this->game->setSession(Game::SS_RESULT, 10);
            $this->previousStatus = Game::SS_RESULT;  // ← TAMBAH INI
            return;
        }

        $answer = GameAnswer::with('question')->find($this->game->current_answer_id);
        if (! $answer || $answer->judged_at) {
            $this->game->setSession(Game::SS_RESULT, 10);
            $this->previousStatus = Game::SS_RESULT;  // ← TAMBAH INI
            return;
        }

        $userAnswer = mb_strtolower(trim($answer->answer_text ?? ''));
        $key = mb_strtolower(trim($answer->question->answer_key));

        $isCorrect = ($userAnswer === $key) || str_contains($userAnswer, $key);

        $answer->is_correct = $isCorrect;
        $answer->judged_at = now();
        $answer->save();

        if ($isCorrect) {
            $group = GameGroup::find($answer->group_id);
            $group->score += $answer->question->points;
            $group->save();
        }

        $this->game->setSession(Game::SS_RESULT, 10);
        $this->previousStatus = Game::SS_RESULT;  // ← TAMBAH INI (PENTING!)
        $this->dispatch('answer-judged', correct: $isCorrect, groupId: $answer->group_id);
    }

    public function markCorrect(): void
    {
        if (! $this->game->current_answer_id) return;
        $answer = GameAnswer::find($this->game->current_answer_id);
        if (! $answer || $answer->judged_at) return;

        if (! $answer->is_correct) {
            $group = GameGroup::find($answer->group_id);
            $group->score += $answer->question->points;
            $group->save();
        }
        $answer->is_correct = true;
        $answer->judged_at = now();
        $answer->save();
        $this->game->setSession(Game::SS_RESULT, 10);
        $this->previousStatus = Game::SS_RESULT;  // ← TAMBAH INI
        $this->dispatch('answer-judged', correct: true, groupId: $answer->group_id);
    }

    public function markWrong(): void
    {
        if (! $this->game->current_answer_id) return;
        $answer = GameAnswer::find($this->game->current_answer_id);
        if (! $answer || $answer->judged_at) return;

        if ($answer->is_correct) {
            $group = GameGroup::find($answer->group_id);
            $group->score -= $answer->question->points;
            $group->save();
        }
        $answer->is_correct = false;
        $answer->judged_at = now();
        $answer->save();
        $this->game->setSession(Game::SS_RESULT, 10);
        $this->previousStatus = Game::SS_RESULT;  // ← TAMBAH INI
        $this->dispatch('answer-judged', correct: false, groupId: $answer->group_id);
    }

    public function reopenBuzzer(): void
    {
        $this->game->buzzer_group_id = null;
        $this->game->current_answer_id = null;
        $this->game->save();
        $this->game->setSession(Game::SS_BUZZER_OPEN);
        $this->previousStatus = Game::SS_BUZZER_OPEN;
    }

    public function autoAdvance(): void
    {
        if ($this->game->session_status === Game::SS_RESULT) {
            $this->revealNextQuestion();
        } elseif ($this->game->session_status === Game::SS_JUDGING) {
            $this->autoJudge();
        }
    }

    public function skipQuestion(): void
    {
        $this->revealNextQuestion();
    }

    public function endGame(): void
    {
        $this->game->status = Game::STATUS_FINISHED;
        $this->game->setSession(Game::SS_FINISHED);

        $groups = $this->game->groups()->orderByDesc('score')->get();
        $rank = 0;
        $prevScore = null;
        foreach ($groups as $i => $g) {
            if ($prevScore === null || $g->score !== $prevScore) {
                $rank = $i + 1;
            }
            $prevScore = $g->score;

            GameHistory::create([
                'game_id'    => $this->game->id,
                'game_name'  => $this->game->name,
                'group_id'   => $g->id,
                'student_id' => $g->leader_id,
                'admin_id'   => $this->game->created_by,
                'score'      => $g->score,
                'rank'       => $rank,
                'role'       => 'leader',
                'played_at'  => now(),
            ]);
        }

        GameHistory::create([
            'game_id'    => $this->game->id,
            'game_name'  => $this->game->name,
            'admin_id'   => $this->game->created_by,
            'score'      => 0,
            'rank'       => 0,
            'role'       => 'admin',
            'played_at'  => now(),
        ]);

        $this->dispatch('game-ended');
    }

    public function render()
    {
        $game = $this->game->fresh(['groups.leader', 'currentQuestion', 'buzzerGroup', 'currentAnswer']);
        return view('livewire.game-host', ['game' => $game]);
    }
}
