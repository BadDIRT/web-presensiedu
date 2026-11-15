<?php

namespace App\Livewire;

use App\Models\Game;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class GameLeaderboard extends Component
{
    public Game $game;

    public function mount(Game $game)
    {
        $this->game = $game;
    }

    public function render()
    {
        $groups = $this->game->groups()->with('leader')->orderByDesc('score')->get();
        return view('livewire.game-leaderboard', compact('groups'));
    }
}
