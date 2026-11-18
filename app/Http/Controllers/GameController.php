<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameGroup;
use App\Models\GameQuestion;
use App\Models\QuestionTemplate;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class GameController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search', '');
        $status = $request->input('status', '');

        $games = Game::withCount(['groups', 'questions'])
            ->with('creator')
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->latest()
            ->paginate(10);

        $games->appends($request->query());

        return view('admin.games.index', compact('games', 'search', 'status'));
    }

    public function create(Request $request): View
    {
        $students = Student::where('is_active', true)->orderBy('name')->get();
        $templates = QuestionTemplate::withCount('items')->latest()->get();
        $selectedTemplate = $request->input('template');

        return view('admin.games.create', compact('students', 'templates', 'selectedTemplate'));
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

        $activeGames = Game::whereHas('groups', function ($q) use ($student) {
            $q->where('leader_id', $student->id);
        })->whereIn('status', [Game::STATUS_ACTIVE, Game::STATUS_DRAFT])->latest()->get();

        $pastGames = \App\Models\GameHistory::where('student_id', $student->id)
            ->latest('played_at')
            ->limit(20)
            ->get();

        return view('siswa.games.index', compact('activeGames', 'pastGames'));
    }

    public function siswaHistory(Game $game): View
    {
        $student = Auth::guard('siswa')->user();
        $history = \App\Models\GameHistory::where('game_id', $game->id)
            ->where('student_id', $student->id)
            ->first();
        return view('siswa.games.history', compact('game', 'history'));
    }
}
