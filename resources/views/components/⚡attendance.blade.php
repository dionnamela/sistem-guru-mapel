<?php

use Flux\Flux;
use Livewire\Component;
use App\Models\Attendance;
use App\Models\AttendanceStudent;
use App\Models\Students;
use App\Models\Rombel;
use App\Models\Mapel;
use Carbon\Carbon;

new class extends Component {
    public $students = [];
    public string $selectedRombel = '';
    public $rombelOptions = [];
    public $mapelOptions = [];
    public string $selectedMapel = '';
    public string $teacher_name = '';
    public string $tanggal = '';
    public array $attendance = [];
    public array $statusOptions = ['Hadir', 'Izin', 'Alpha', 'Sakit'];

    protected array $rules = [
        'selectedRombel' => 'required|string|max:255',
        'selectedMapel' => 'required|string|max:255',
        'teacher_name' => 'required|string|max:255',
        'tanggal' => 'required|date',
        'attendance' => 'required|array',
    ];

    public function mount(): void
    {
        $this->tanggal = Carbon::now()->format('Y-m-d');

        $this->teacher_name = auth()->user()
            ? auth()->user()->name
            : '';

        $this->loadRombelOptions();
        $this->loadMapelOptions();
    }

    private function loadRombelOptions(): void
    {
        $this->rombelOptions = Rombel::orderBy('nama')
            ->pluck('nama')
            ->toArray();
    }

    private function loadMapelOptions(): void
    {
        $this->mapelOptions = Mapel::orderBy('nama')
            ->pluck('nama')
            ->toArray();
    }

    public function updatedSelectedRombel(): void
    {
        $this->loadStudents();
    }

    public function loadStudents(): void
    {
        if ($this->selectedRombel === '') {
            $this->students = [];
            return;
        }

        $this->students = Students::where('rombel', $this->selectedRombel)
            ->orderBy('nama')
            ->get();

        foreach ($this->students as $student) {
            if (! array_key_exists($student->id, $this->attendance)) {
                $this->attendance[$student->id] = [
                    'status' => 'Hadir',
                    'keterangan' => '',
                ];
            }
        }
    }

    public function saveAttendance(): void
    {
        $validated = $this->validate();

        if (count($this->students) === 0) {
            Flux::toast(variant: 'warning', text: 'Pilih rombel terlebih dahulu sebelum menyimpan absensi.');
            return;
        }

        $attendance = Attendance::create([
            'rombel' => $validated['selectedRombel'],
            'mapel' => $validated['selectedMapel'],
            'teacher_name' => $validated['teacher_name'],
            'tanggal' => $validated['tanggal'],
        ]);
        foreach ($this->students as $student) {
            $row = $this->attendance[$student->id] ?? ['status' => 'Hadir', 'keterangan' => ''];

            AttendanceStudent::create([
                'attendance_id' => $attendance->id,
                'student_id' => $student->id,
                'status' => in_array($row['status'], $this->statusOptions, true) ? $row['status'] : 'Hadir',
                'keterangan' => trim((string) ($row['keterangan'] ?? '')) ?: null,
            ]);
        }

        $this->selectedRombel = '';
        $this->selectedMapel = '';
        $this->students = [];
        $this->attendance = [];

        $this->loadRombelOptions();
        $this->loadMapelOptions();
        $this->tanggal = Carbon::now()->format('Y-m-d');

        Flux::toast(variant: 'success', text: 'Absensi berhasil disimpan.');
    }
};
?>

<div class="p-6 bg-zinc-900 rounded-2xl shadow-2xl border border-zinc-800">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-white">
                Absen Siswa
            </h1>

            <p class="text-zinc-400 mt-1">
                Pilih rombel terlebih dahulu untuk melihat siswa yang hadir.
            </p>
        </div>

        <button
            type="button"
            wire:click="saveAttendance"
            class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-emerald-500 text-white text-sm font-medium hover:bg-emerald-400 transition duration-200 shadow-lg">
            Simpan Absensi
        </button>
    </div>

    <div class="grid gap-4 md:grid-cols-4 mb-6">
        <div>
            <label class="block text-sm font-medium text-zinc-300">Rombel</label>
            <select
                wire:model="selectedRombel"
                wire:change="loadStudents"
                class="w-full rounded-xl border border-zinc-700 bg-zinc-950 px-3 py-2 text-white focus:border-indigo-500 focus:outline-none">
                <option value="">Pilih Rombel</option>
                @foreach ($rombelOptions as $rombelOption)
                <option value="{{ $rombelOption }}">{{ $rombelOption }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-zinc-300">
                Mata Pelajaran
            </label>

            <select
                wire:model="selectedMapel"
                class="w-full rounded-xl border border-zinc-700 bg-zinc-950 px-3 py-2 text-white focus:border-indigo-500 focus:outline-none">

                <option value="">
                    Pilih Mata Pelajaran
                </option>

                @foreach ($mapelOptions as $mapelOption)
                <option value="{{ $mapelOption }}">
                    {{ $mapelOption }}
                </option>
                @endforeach

            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-zinc-300">Nama Guru</label>
            <input
                type="text"
                wire:model.defer="teacher_name"
                class="w-full rounded-xl border border-zinc-700 bg-zinc-950 px-3 py-2 text-white focus:border-indigo-500 focus:outline-none"
                placeholder="Nama guru" />
        </div>

        <div>
            <label class="block text-sm font-medium text-zinc-300">Tanggal</label>
            <input
                type="date"
                wire:model.defer="tanggal"
                class="w-full rounded-xl border border-zinc-700 bg-zinc-950 px-3 py-2 text-white focus:border-indigo-500 focus:outline-none" />
        </div>
    </div>

    <div class="overflow-x-auto rounded-xl border border-zinc-800">
        <table class="min-w-full">
            <thead class="bg-zinc-800 text-zinc-300">
                <tr>
                    <th class="px-5 py-4 text-left text-sm font-semibold">No</th>
                    <th class="px-5 py-4 text-left text-sm font-semibold">Nama</th>
                    <th class="px-5 py-4 text-left text-sm font-semibold">NISN</th>
                    <th class="px-5 py-4 text-left text-sm font-semibold">Status</th>
                    <th class="px-5 py-4 text-left text-sm font-semibold">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800">
                @if (count($students) === 0)
                <tr>
                    <td colspan="5" class="px-5 py-10 text-center text-zinc-400">
                        Pilih rombel untuk menampilkan siswa.
                    </td>
                </tr>
                @else
                @foreach ($students as $index => $student)
                <tr class="hover:bg-zinc-800/60 transition duration-200">
                    <td class="px-5 py-4 text-zinc-300">{{ $index + 1 }}</td>
                    <td class="px-5 py-4">
                        <div class="font-semibold text-white">{{ $student->nama }}</div>
                    </td>
                    <td class="px-5 py-4 text-zinc-300">{{ $student->nisn }}</td>
                    <td class="px-5 py-4">
                        <select
                            wire:model.defer="attendance.{{ $student->id }}.status"
                            class="w-full rounded-xl border border-zinc-700 bg-zinc-950 px-3 py-2 text-white focus:border-indigo-500 focus:outline-none">
                            @foreach ($statusOptions as $status)
                            <option value="{{ $status }}">{{ $status }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="px-5 py-4">
                        <input
                            type="text"
                            wire:model.defer="attendance.{{ $student->id }}.keterangan"
                            class="w-full rounded-xl border border-zinc-700 bg-zinc-950 px-3 py-2 text-white focus:border-indigo-500 focus:outline-none"
                            placeholder="Keterangan (misalnya izin)" />
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>