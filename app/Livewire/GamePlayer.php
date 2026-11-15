<?php

namespace App\Livewire;

use App\Models\Game;
use Livewire\Attributes\Layout;
use App\Models\GameAnswer;
use App\Models\GameGroup;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

#[Layout('layouts.app')]
class GamePlayer extends Component
{
    public Game $game;
    public ?int $myGroupId = null;
    public string $answerText = '';

    // Listen event dari GameHost (via polling sync)
    protected $listeners = ['answer-received'];

    public function mount(Game $game)
    {
        $this->game = $game;
        $student = Auth::guard('siswa')->user();
        $group = GameGroup::where('game_id', $game->id)
            ->where('leader_id', $student->id)
            ->first();
        $this->myGroupId = $group?->id;
    }

    public function refreshState(): void
    {
        $this->game->refresh();
    }

    /**
     * Leader tekan buzzer.
     */
    public function pressBuzzer(): void
    {
        if (! $this->myGroupId) return;
        if ($this->game->session_status !== Game::SS_BUZZER_OPEN) return;
        if ($this->game->buzzer_group_id) return; // sudah ada yang menang

        // Panggil method di GameHost (via global Livewire event)
        $this->dispatch('player-buzzed', groupId: $this->myGroupId)->to(GameHost::class);

        // Fallback: langsung update DB di sini juga (untuk konsistensi)
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
        }
    }

    /**
     * Submit jawaban.
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

        $this->game->setSession(Game::SS_JUDGING);

        // Auto-judge (case-insensitive)
        $userAnswer = mb_strtolower(trim($answer->answer_text));
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
        $this->answerText = '';
        $this->dispatch('answer-judged', correct: $isCorrect);
    }

    public function render()
    {
        $game = $this->game->fresh(['groups.leader', 'currentQuestion', 'buzzerGroup', 'currentAnswer']);
        $myGroup = $this->myGroupId ? GameGroup::with('leader')->find($this->myGroupId) : null;
        return view('livewire.game-player', compact('game', 'myGroup'));
    }
}
