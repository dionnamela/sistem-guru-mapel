<?php

use Flux\Flux;
use Livewire\Component;
use App\Models\Students;

new class extends Component {
    public $students = [];
    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingStudentId = null;
    public ?int $deletingStudentId = null;
    public string $nama = '';
    public string $nisn = '';
    public string $rombel = '';
    public string $jenis_kelamin = 'L';

    protected array $rules = [
        'nama' => 'required|string|max:255',
        'nisn' => 'required|string|max:20|unique:students,nisn',
        'rombel' => 'required|string|max:255',
        'jenis_kelamin' => 'required|in:L,P',
    ];

    public function mount()
    {
        $this->students = Students::all();
    }

    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->nama = '';
        $this->nisn = '';
        $this->rombel = '';
        $this->jenis_kelamin = 'L';
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
    }

    public function openEditModal(int $studentId): void
    {
        $student = Students::findOrFail($studentId);

        $this->resetValidation();
        $this->editingStudentId = $student->id;
        $this->nama = $student->nama;
        $this->nisn = $student->nisn;
        $this->rombel = $student->rombel;
        $this->jenis_kelamin = $student->jenis_kelamin;
        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->editingStudentId = null;
    }

    public function openDeleteModal(int $studentId): void
    {
        $this->deletingStudentId = $studentId;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->deletingStudentId = null;
    }

    public function createStudent(): void
    {
        $validated = $this->validate();

        Students::create([
            'nama' => $validated['nama'],
            'nisn' => $validated['nisn'],
            'rombel' => $validated['rombel'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
        ]);

        $this->students = Students::all();
        $this->closeCreateModal();

        Flux::toast(variant: 'success', text: 'Siswa berhasil ditambahkan.');
    }

    public function deleteStudent(): void
    {
        if (! $this->deletingStudentId) {
            return;
        }

        Students::findOrFail($this->deletingStudentId)->delete();

        $this->students = Students::all();
        $this->closeDeleteModal();

        Flux::toast(variant: 'success', text: 'Siswa berhasil dihapus.');
    }

    public function updateStudent(): void
    {
        if (! $this->editingStudentId) {
            return;
        }

        $validated = $this->validate([
            'nama' => 'required|string|max:255',
            'nisn' => 'required|string|max:20|unique:students,nisn,' . $this->editingStudentId,
            'rombel' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
        ]);

        Students::findOrFail($this->editingStudentId)->update([
            'nama' => $validated['nama'],
            'nisn' => $validated['nisn'],
            'rombel' => $validated['rombel'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
        ]);

        $this->students = Students::all();
        $this->closeEditModal();

        Flux::toast(variant: 'success', text: 'Siswa berhasil diperbarui.');
    }
};
?>

<div class="p-6 bg-zinc-900 rounded-2xl shadow-2xl border border-zinc-800">

    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-white">
                Data Siswa
            </h1>

            <p class="text-zinc-400 mt-1">
                Daftar seluruh siswa
            </p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <button
                type="button"
                wire:click="openCreateModal"
                class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-indigo-500 text-white text-sm font-medium hover:bg-indigo-400 transition duration-200 shadow-lg">
                Tambah Siswa
            </button>

            <div class="px-4 py-2 bg-indigo-500/20 text-indigo-400 rounded-xl text-sm font-medium">
                Total: {{ count($students) }} siswa
            </div>
        </div>
    </div>

    <div class="overflow-x-auto rounded-xl border border-zinc-800">

        <table class="min-w-full">

            <thead class="bg-zinc-800 text-zinc-300">
                <tr>
                    <th class="px-5 py-4 text-left text-sm font-semibold">
                        No
                    </th>

                    <th class="px-5 py-4 text-left text-sm font-semibold">
                        Nama
                    </th>

                    <th class="px-5 py-4 text-left text-sm font-semibold">
                        NISN
                    </th>

                    <th class="px-5 py-4 text-left text-sm font-semibold">
                        Rombel
                    </th>

                    <th class="px-5 py-4 text-left text-sm font-semibold">
                        Jenis Kelamin
                    </th>

                    <th class="px-5 py-4 text-center text-sm font-semibold">
                        Action
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-zinc-800">

                @forelse ($students as $index => $student)
                <tr class="hover:bg-zinc-800/60 transition duration-200">

                    <td class="px-5 py-4 text-zinc-300">
                        {{ $index + 1 }}
                    </td>

                    <td class="px-5 py-4">
                        <div class="font-semibold text-white">
                            {{ $student->nama }}
                        </div>
                    </td>

                    <td class="px-5 py-4 text-zinc-300">
                        {{ $student->nisn }}
                    </td>

                    <td class="px-5 py-4 text-zinc-300">
                        {{ $student->rombel }}
                    </td>

                    <td class="px-5 py-4 text-zinc-300">
                        {{ $student->jenis_kelamin }}
                    </td>

                    <td class="px-5 py-4">

                        <div class="flex items-center justify-center gap-3">

                            <button
                                type="button"
                                wire:click="openEditModal({{ $student->id }})"
                                class="px-4 py-2 rounded-xl bg-zinc-700 hover:bg-zinc-600 text-white text-sm font-medium transition duration-200 shadow-lg">
                                Edit
                            </button>

                            <button
                                type="button"
                                wire:click="openDeleteModal({{ $student->id }})"
                                class="px-4 py-2 rounded-xl bg-red-500/20 hover:bg-red-500 text-red-400 hover:text-white text-sm font-medium transition duration-200 shadow-lg">
                                Hapus
                            </button>

                        </div>

                    </td>

                </tr>

                @empty

                <tr>
                    <td colspan="6" class="px-5 py-10 text-center">

                        <div class="flex flex-col items-center">

                            <div class="text-5xl mb-3">
                                📚
                            </div>

                            <p class="text-zinc-400 text-lg">
                                Data siswa belum tersedia
                            </p>

                        </div>

                    </td>
                </tr>
                @endforelse

            </tbody>

        </table>

    </div>

    <flux:modal
        name="create-student-modal"
        class="max-w-2xl"
        @close="closeCreateModal"
        wire:model="showCreateModal">
        <div class="space-y-6">
            <div class="space-y-2">
                <flux:heading size="lg">Tambah Siswa</flux:heading>
                <flux:text>Isi data siswa baru untuk menambahkannya ke dalam daftar.</flux:text>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input
                    wire:model.defer="nama"
                    label="Nama"
                    required />
                <flux:input
                    wire:model.defer="nisn"
                    label="NISN"
                    required />
                <flux:input
                    wire:model.defer="rombel"
                    label="Rombel"
                    required />
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-zinc-300">Jenis Kelamin</label>
                    <select
                        wire:model.defer="jenis_kelamin"
                        class="w-full rounded-xl border border-zinc-700 bg-zinc-950 px-3 py-2 text-white focus:border-indigo-500 focus:outline-none">
                        <option value="L">L</option>
                        <option value="P">P</option>
                    </select>
                    @error('jenis_kelamin')
                    <div class="text-sm text-red-500">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                <button
                    type="button"
                    wire:click="closeCreateModal"
                    class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-zinc-700 text-white text-sm font-medium hover:bg-zinc-600 transition duration-200">
                    Batal
                </button>
                <button
                    type="button"
                    wire:click="createStudent"
                    class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-indigo-500 text-white text-sm font-medium hover:bg-indigo-400 transition duration-200">
                    Simpan Siswa
                </button>
            </div>
        </div>
    </flux:modal>

    <flux:modal
        name="edit-student-modal"
        class="max-w-2xl"
        @close="closeEditModal"
        wire:model="showEditModal">
        <div class="space-y-6">
            <div class="space-y-2">
                <flux:heading size="lg">Edit Siswa</flux:heading>
                <flux:text>Perbarui informasi siswa yang sudah ada.</flux:text>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input
                    wire:model.defer="nama"
                    label="Nama"
                    required />
                <flux:input
                    wire:model.defer="nisn"
                    label="NISN"
                    required />
                <flux:input
                    wire:model.defer="rombel"
                    label="Rombel"
                    required />
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-zinc-300">Jenis Kelamin</label>
                    <select
                        wire:model.defer="jenis_kelamin"
                        class="w-full rounded-xl border border-zinc-700 bg-zinc-950 px-3 py-2 text-white focus:border-indigo-500 focus:outline-none">
                        <option value="L">L</option>
                        <option value="P">P</option>
                    </select>
                    @error('jenis_kelamin')
                    <div class="text-sm text-red-500">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                <button
                    type="button"
                    wire:click="closeEditModal"
                    class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-zinc-700 text-white text-sm font-medium hover:bg-zinc-600 transition duration-200">
                    Batal
                </button>
                <button
                    type="button"
                    wire:click="updateStudent"
                    class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-indigo-500 text-white text-sm font-medium hover:bg-indigo-400 transition duration-200">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </flux:modal>

    <flux:modal
        name="delete-student-modal"
        class="max-w-md"
        @close="closeDeleteModal"
        wire:model="showDeleteModal">
        <div class="space-y-6">
            <div class="space-y-2">
                <flux:heading size="lg">Hapus Siswa</flux:heading>
                <flux:text>Apakah Anda yakin ingin menghapus siswa ini? Perubahan tidak dapat dibatalkan.</flux:text>
            </div>

            <div class="flex gap-3 justify-end">
                <flux:button
                    variant="outline"
                    wire:click="closeDeleteModal">
                    Batal
                </flux:button>
                <flux:button
                    variant="danger"
                    wire:click="deleteStudent">
                    Hapus Siswa
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>