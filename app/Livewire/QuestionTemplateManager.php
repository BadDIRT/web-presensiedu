<?php

namespace App\Livewire;

use App\Models\QuestionTemplate;
use App\Models\QuestionTemplateItem;
use Livewire\Component;
use Livewire\WithPagination;

class QuestionTemplateManager extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?int $editId = null;

    public string $name = '';
    public string $description = '';
    public array $items = [];

    protected $listeners = ['template-saved' => '$refresh'];

    public function mount(): void
    {
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->name = '';
        $this->description = '';
        $this->editId = null;
        $this->items = [];
        for ($p = 1; $p <= 3; $p++) {
            for ($o = 1; $o <= 10; $o++) {
                $this->items[] = [
                    'phase' => $p,
                    'order' => $o,
                    'question_text' => '',
                    'answer_key' => '',
                    'points' => $p === 1 ? 10 : ($p === 2 ? 15 : 20),
                ];
            }
        }
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $template = QuestionTemplate::with('items')->findOrFail($id);
        $this->editId = $id;
        $this->name = $template->name;
        $this->description = $template->description ?? '';

        $this->items = [];
        for ($p = 1; $p <= 3; $p++) {
            for ($o = 1; $o <= 10; $o++) {
                $item = $template->items->where('phase', $p)->where('order', $o)->first();
                $this->items[] = [
                    'phase' => $p,
                    'order' => $o,
                    'question_text' => $item?->question_text ?? '',
                    'answer_key' => $item?->answer_key ?? '',
                    'points' => $item?->points ?? ($p === 1 ? 10 : ($p === 2 ? 15 : 20)),
                ];
            }
        }
        $this->showModal = true;
    }

    public function save(): void
    {
        // Validasi — hanya cek yang diisi saja (tidak wajib semua 30)
        $this->validate([
            'name' => 'required|string|min:3|max:100',
            'items' => 'required|array',
            'items.*.question_text' => 'nullable|string',
            'items.*.answer_key' => 'nullable|string|max:200',
            'items.*.points' => 'required|integer|min:1|max:100',
        ]);

        if ($this->editId) {
            $template = QuestionTemplate::findOrFail($this->editId);
            $template->update([
                'name' => $this->name,
                'description' => $this->description,
            ]);
            $template->items()->delete();
        } else {
            $template = QuestionTemplate::create([
                'name' => $this->name,
                'description' => $this->description,
                'created_by' => auth()->guard('web')->id(),
            ]);
        }

        // Hanya simpan item yang punya question_text DAN answer_key
        foreach ($this->items as $item) {
            $q = trim($item['question_text'] ?? '');
            $a = trim($item['answer_key'] ?? '');
            if ($q !== '' && $a !== '') {
                QuestionTemplateItem::create([
                    'template_id' => $template->id,
                    'phase' => $item['phase'],
                    'order' => $item['order'],
                    'question_text' => $q,
                    'answer_key' => $a,
                    'points' => $item['points'],
                ]);
            }
        }

        $this->dispatch(
            'scan-success',
            message: $this->editId ? 'Template berhasil diupdate!' : 'Template berhasil dibuat!'
        );
        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        QuestionTemplate::findOrFail($id)->delete();
        $this->dispatch('scan-success', message: 'Template dihapus.');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function render()
    {
        $templates = QuestionTemplate::withCount('items')
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);

        return view('livewire.question-template-manager', compact('templates'));
    }
}
