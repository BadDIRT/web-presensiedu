<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameGroup;
use App\Models\GameHistory;
use App\Models\GameQuestion;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Livewire\GameHost as GameHostComponent;
use App\Livewire\GameLeaderboard as GameLeaderboardComponent;
use App\Livewire\GamePlayer as GamePlayerComponent;

class GameController extends Controller
{
    public function index(): View
    {
        $games = Game::withCount(['groups', 'questions'])->latest()->get();
        return view('admin.games.index', compact('games'));
    }

    public function create(): View
    {
        $students = Student::where('is_active', true)->orderBy('name')->get();
        return view('admin.games.create', compact('students'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:100',
            'groups'             => 'required|array|min:1',
            'groups.*.name'     => 'required|string|max:50',
            'groups.*.leader_id' => 'required|exists:students,id',
            'groups.*.color'    => 'nullable|string|max:20',
            'questions'          => 'required|array',
            'questions.*.phase'  => 'required|in:1,2,3',
            'questions.*.order' => 'required|integer|min:1|max:10',
            'questions.*.question_text' => 'required|string',
            'questions.*.answer_key'  => 'required|string|max:200',
            'questions.*.points'      => 'nullable|integer|min:1|max:100',
        ]);

        $game = Game::create([
            'name'       => $data['name'],
            'status'     => Game::STATUS_DRAFT,
            'created_by' => Auth::guard('web')->id(),
            'session_status' => Game::SS_LOBBY,
        ]);

        foreach ($data['groups'] as $g) {
            GameGroup::create([
                'game_id'   => $game->id,
                'name'      => $g['name'],
                'leader_id' => $g['leader_id'],
                'color'     => $g['color'] ?? '#ffcc33',
                'score'     => 0,
            ]);
        }

        foreach ($data['questions'] as $q) {
            GameQuestion::create([
                'game_id'       => $game->id,
                'phase'         => $q['phase'],
                'order'         => $q['order'],
                'question_text' => $q['question_text'],
                'answer_key'    => trim($q['answer_key']),
                'points'        => $q['points'] ?? 10,
            ]);
        }

        return redirect()->route('admin.games.show', $game)->with('success', 'Game berhasil dibuat.');
    }

    public function show(Game $game): View
    {
        $game->load(['groups.leader', 'questions']);
        return view('admin.games.show', compact('game'));
    }

    public function destroy(Game $game)
    {
        $game->delete();
        return redirect()->route('admin.games.index')->with('success', 'Game dihapus.');
    }

    public function siswaIndex(): View
    {
        $student = Auth::guard('siswa')->user();

        // Game di mana siswa ini adalah leader dari salah satu group
        $activeGames = Game::whereHas('groups', function ($q) use ($student) {
            $q->where('leader_id', $student->id);
        })->whereIn('status', [Game::STATUS_ACTIVE, Game::STATUS_DRAFT])->latest()->get();

        $pastGames = GameHistory::where('student_id', $student->id)
            ->latest('played_at')
            ->limit(20)
            ->get();

        return view('siswa.games.index', compact('activeGames', 'pastGames'));
    }

    public function siswaHistory(Game $game): View
    {
        $student = Auth::guard('siswa')->user();
        $history = GameHistory::where('game_id', $game->id)
            ->where('student_id', $student->id)
            ->first();
        return view('siswa.games.history', compact('game', 'history'));
    }

    // Tambah method ini di dalam class
    public function host(Game $game): View
    {
        return view('admin.games.host', compact('game'));
    }

    public function leaderboard(Game $game): View
    {
        return view('admin.games.leaderboard', compact('game'));
    }

    public function play(Game $game): View
    {
        return view('siswa.games.play', compact('game'));
    }
}
