<?php

namespace App\Http\Controllers;

use App\Models\QuestionTemplate;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuestionTemplateController extends Controller
{
    public function index(): View
    {
        $templates = QuestionTemplate::withCount('items')
            ->with('creator')
            ->latest()
            ->paginate(10);

        return view('admin.templates.index', compact('templates'));
    }

    public function show(QuestionTemplate $template): View
    {
        $template->load('items');
        return view('admin.templates.show', compact('template'));
    }

    public function destroy(QuestionTemplate $template)
    {
        $template->delete();
        return redirect()->route('admin.templates.index')
            ->with('success', 'Template dihapus.');
    }
}
