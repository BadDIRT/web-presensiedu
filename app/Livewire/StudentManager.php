<?php

namespace App\Livewire;

use App\Models\Student;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class StudentManager extends Component
{
    use WithPagination;

    // Form fields
    #[Validate('required|string|min:3|max:100')]
    public string $name = '';

    #[Validate('required|string|size:6|unique:students,id_siswa')]
    public string $id_siswa = '';

    #[Validate('nullable|string|max:50')]
    public string $kelas = 'Jarimath';

    public bool $is_active = true;

    // State
    public bool $showModal       = false;
    public bool $showPassword    = false; // tampilkan password hasil generate
    public ?string $createdPassword = null; // password yang baru di-generate
    public ?int $editId = null;
    public string $search = '';

    // Listen refresh dari action lain
    protected $listeners = ['student-saved' => '$refresh'];

    public function render()
    {
        $students = Student::query()
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('id_siswa', 'like', "%{$this->search}%")
                    ->orWhere('kelas', 'like', "%{$this->search}%");
            })
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.student-manager', compact('students'));
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        // Auto-generate ID siswa baru (bisa diubah admin)
        $this->id_siswa = Student::generateUniqueStudentId();
        $this->editId   = null;
        $this->showModal = true;
        $this->createdPassword = null;
        $this->showPassword = false;
    }

    public function openEditModal(int $id): void
    {
        $student = Student::findOrFail($id);
        $this->editId     = $id;
        $this->name      = $student->name;
        $this->id_siswa  = $student->id_siswa;
        $this->kelas     = $student->kelas ?? 'Jarimath';
        $this->is_active = (bool) $student->is_active;
        $this->showModal = true;
        $this->createdPassword = null;
        $this->showPassword = false;
    }

    public function save(): void
    {
        // Validasi unique id_siswa tapi abaikan dirinya sendiri saat edit
        $rules = [
            'name'      => 'required|string|min:3|max:100',
            'id_siswa'  => 'required|string|size:6|unique:students,id_siswa,' . ($this->editId ?? 'NULL') . ',id',
            'kelas'     => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ];
        $validated = $this->validate($rules);

        if ($this->editId) {
            // Update existing
            $student = Student::findOrFail($this->editId);
            $student->update([
                'name'      => $validated['name'],
                'id_siswa'  => $validated['id_siswa'],
                'kelas'     => $validated['kelas'] ?: 'Jarimath',
                'is_active' => (bool) $validated['is_active'],
            ]);
            $this->dispatch('scan-success', message: "Data siswa {$student->name} berhasil diupdate.");
        } else {
            // Create new — auto-generate password
            $password = Student::generatePassword(8);
            $student = Student::create([
                'name'      => $validated['name'],
                'id_siswa'  => $validated['id_siswa'],
                'kelas'     => $validated['kelas'] ?: 'Jarimath',
                'password'  => $password,
                'is_active' => (bool) $validated['is_active'],
            ]);
            // Tampilkan password ke admin agar disampaikan ke siswa
            $this->createdPassword = $password;
            $this->showPassword = true;
            $this->dispatch(
                'scan-success',
                message: "Siswa {$student->name} ditambahkan. Password sementara: {$password}"
            );
        }

        if (! $this->showPassword) {
            $this->showModal = false;
            $this->resetForm();
        }
        $this->dispatch('attendance-added', userId: 0, status: 'hadir');
    }

    public function regeneratePassword(int $id): void
    {
        $student = Student::findOrFail($id);
        $newPass = Student::generatePassword(8);
        $student->update(['password' => $newPass]);
        $this->createdPassword = $newPass;
        $this->showPassword = true;
        $this->dispatch(
            'scan-success',
            message: "Password {$student->name} direset ke: {$newPass}"
        );
    }

    public function toggleActive(int $id): void
    {
        $s = Student::findOrFail($id);
        $s->update(['is_active' => ! $s->is_active]);
        $this->dispatch(
            'scan-success',
            message: $s->name . ' ' . ($s->is_active ? 'diaktifkan' : 'dinonaktifkan')
        );
    }

    public function delete(int $id): void
    {
        $s = Student::findOrFail($id);
        $nama = $s->name;
        $s->delete();
        $this->dispatch('scan-success', message: "Siswa {$nama} dihapus.");
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->reset(['name', 'id_siswa', 'kelas', 'is_active', 'editId', 'createdPassword', 'showPassword']);
        $this->kelas = 'Jarimath';
        $this->is_active = true;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }
}
