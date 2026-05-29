<?php

use Flux\Flux;
use Livewire\Component;
use App\Models\Students;
use App\Models\Mapel;
use App\Models\Rombel;
use App\Models\Score;
use App\Models\AttendanceStudent;

new class extends Component
{
    public string $selectedRombel = '';

    public string $selectedMapel = '';

    public $mapelOptions = [];

    public $rombelOptions = [];

    public $students = [];

    public array $scores = [];

    public function mount()
    {
        $this->mapelOptions = Mapel::orderBy('nama')
            ->get();

        $this->rombelOptions = Rombel::orderBy('nama')
            ->get();
    }

    public function updatedSelectedRombel()
    {
        $this->students = Students::where(
            'rombel',
            $this->selectedRombel
        )
            ->orderBy('nama')
            ->get();

        foreach ($this->students as $student) {

            $this->scores[$student->id] = [
                'tugas' => 0,
                'uh' => 0,
                'mid' => 0,
                'uas' => 0,
            ];
        }
    }

    public function save()
    {
        foreach ($this->students as $student) {

            $nilai = $this->scores[$student->id];

            $totalPertemuan = AttendanceStudent::where(
                'student_id',
                $student->id
            )->count();

            $totalHadir = AttendanceStudent::where(
                'student_id',
                $student->id
            )
                ->where('status', 'Hadir')
                ->count();

            $nilaiAbsen = $totalPertemuan > 0
                ? round(($totalHadir / $totalPertemuan) * 100)
                : 0;

            $rataRata = round(
                (
                    (int) ($nilai['tugas'] ?? 0) +
                    (int) ($nilai['uh'] ?? 0)
                ) / 2
            );

            $nilaiAkhir = round(
                (
                    ((int) $nilai['tugas'] * 0.20) +
                    ((int) $nilai['uh'] * 0.25) +
                    ((int) $nilai['mid'] * 0.25) +
                    ((int) $nilai['uas'] * 0.20) +
                    ($nilaiAbsen * 0.10)
                )
            );

            Score::updateOrCreate(

                [
                    'student_id' => $student->id,
                    'mata_pelajaran' => $this->selectedMapel,
                ],

                [
                    'rombel' => $student->rombel,

                    'nilai_tugas' => $nilai['tugas'],

                    'nilai_uh' => $nilai['uh'],

                    'nilai_mid' => $nilai['mid'],

                    'nilai_uas' => $nilai['uas'],

                    'nilai_absen' => $nilaiAbsen,

                    'rata_rata' => $rataRata,

                    'nilai_akhir' => $nilaiAkhir,
                ]
            );
        }

        Flux::toast(
            variant: 'success',
            text: 'Nilai berhasil disimpan.'
        );
    }
};
?>

<div class="space-y-6">

    {{-- Header --}}
    <div class="rounded-3xl border border-zinc-800 bg-zinc-900 p-6">

        <h1 class="text-3xl font-bold text-white">
            Input Nilai Siswa
        </h1>

        <p class="mt-2 text-zinc-400">
            Input nilai tugas, UH, MID, dan UAS siswa.
        </p>

    </div>

    {{-- Form --}}
    <div class="rounded-3xl border border-zinc-800 bg-zinc-900 p-6">

        <div class="grid gap-4 md:grid-cols-2">

            {{-- Rombel --}}
            <div>

                <label class="mb-2 block text-sm font-medium text-zinc-300">
                    Rombel
                </label>

                <select
                    wire:model.live="selectedRombel"
                    class="w-full rounded-2xl border border-zinc-700 bg-zinc-950 px-4 py-3 text-white">

                    <option value="">
                        Pilih Rombel
                    </option>

                    @foreach ($rombelOptions as $rombel)
                    <option value="{{ $rombel->nama }}">
                        {{ $rombel->nama }}
                    </option>
                    @endforeach

                </select>

            </div>

            {{-- Mata Pelajaran --}}
            <div>
                <label class="mb-2 block text-sm font-semibold text-zinc-300">
                    Mata Pelajaran
                </label>

                <div class="relative">

                    <select
                        wire:model.live="selectedMapel"
                        class="w-full appearance-none rounded-2xl border border-zinc-700 bg-zinc-950 px-4 py-3 pr-12 text-white shadow-lg transition duration-200 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">

                        <option value="">
                            Pilih Mata Pelajaran
                        </option>

                        @foreach ($mapelOptions as $mapel)
                        <option value="{{ $mapel->nama }}">
                            {{ $mapel->nama }}
                        </option>
                        @endforeach

                    </select>

                    {{-- Icon --}}
                    <div class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-zinc-400">
                    </div>

                </div>
            </div>
        </div>

    </div>

    {{-- Tabel --}}
    <div class="overflow-x-auto rounded-3xl border border-zinc-800 bg-zinc-900">

        <table class="min-w-full">

            <thead class="bg-zinc-800 text-zinc-300">

                <tr>

                    <th class="px-5 py-4 text-left">
                        No
                    </th>

                    <th class="px-5 py-4 text-left">
                        Nama
                    </th>

                    <th class="px-5 py-4 text-left">
                        Tugas
                    </th>

                    <th class="px-5 py-4 text-left">
                        UH
                    </th>

                    <th class="px-5 py-4 text-left">
                        MID
                    </th>

                    <th class="px-5 py-4 text-left">
                        UAS
                    </th>

                    <th class="px-5 py-4 text-left">
                        Rata-rata
                    </th>
                    <th class="px-4 py-3 text-left">
                        Absen
                    </th>

                    <th class="px-5 py-4 text-left">
                        Nilai Akhir
                    </th>

                </tr>

            </thead>

            <tbody class="divide-y divide-zinc-800">

                @forelse ($students as $index => $student)

                @php

                $nilai = $scores[$student->id] ?? [
                'tugas' => 0,
                'uh' => 0,
                'mid' => 0,
                'uas' => 0,
                ];

                $rata = round(
                (
                (int) ($nilai['tugas'] ?? 0) +
                (int) ($nilai['uh'] ?? 0)
                ) / 2
                );

                $totalPertemuan = \App\Models\AttendanceStudent::where(
                'student_id',
                $student->id
                )->count();

                $totalHadir = \App\Models\AttendanceStudent::where(
                'student_id',
                $student->id
                )
                ->where('status', 'Hadir')
                ->count();

                $nilaiAbsen = $totalPertemuan > 0
                ? round(($totalHadir / $totalPertemuan) * 100)
                : 0;

                $akhir = round(
                (
                ((int) $nilai['tugas'] * 0.20) +
                ((int) $nilai['uh'] * 0.25) +
                ((int) $nilai['mid'] * 0.25) +
                ((int) $nilai['uas'] * 0.20) +
                ($nilaiAbsen * 0.10)
                )
                );

                @endphp

                <tr class="hover:bg-zinc-800/40">

                    <td class="px-5 py-4 text-zinc-300">
                        {{ $index + 1 }}
                    </td>

                    <td class="px-5 py-4 text-white">
                        {{ $student->nama }}
                    </td>

                    {{-- Tugas --}}
                    <td class="px-5 py-4">
                        <input
                            type="number"
                            min="0"
                            max="100"
                            wire:model.live="scores.{{ $student->id }}.tugas"
                            class="w-24 rounded-xl border border-zinc-700 bg-zinc-950 px-3 py-2 text-white">
                    </td>

                    {{-- UH --}}
                    <td class="px-5 py-4">
                        <input
                            type="number"
                            min="0"
                            max="100"
                            wire:model.live="scores.{{ $student->id }}.uh"
                            class="w-24 rounded-xl border border-zinc-700 bg-zinc-950 px-3 py-2 text-white">
                    </td>

                    {{-- MID --}}
                    <td class="px-5 py-4">
                        <input
                            type="number"
                            min="0"
                            max="100"
                            wire:model.live="scores.{{ $student->id }}.mid"
                            class="w-24 rounded-xl border border-zinc-700 bg-zinc-950 px-3 py-2 text-white">
                    </td>

                    {{-- UAS --}}
                    <td class="px-5 py-4">
                        <input
                            type="number"
                            min="0"
                            max="100"
                            wire:model.live="scores.{{ $student->id }}.uas"
                            class="w-24 rounded-xl border border-zinc-700 bg-zinc-950 px-3 py-2 text-white">
                    </td>

                    {{-- Rata-rata --}}
                    <td class="px-5 py-4 text-yellow-400 font-semibold">
                        {{ $rata }}
                    </td>
                    <td class="px-4 py-3 text-white">
                        {{ $nilaiAbsen }}%
                    </td>
                    {{-- Nilai Akhir --}}
                    <td class="px-5 py-4 text-green-400 font-bold">
                        {{ $akhir }}
                    </td>

                </tr>

                @empty

                <tr>

                    <td
                        colspan="8"
                        class="px-5 py-10 text-center text-zinc-400">

                        Pilih rombel terlebih dahulu.

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    {{-- Button --}}
    <div class="flex justify-end">

        <button
            wire:click="save"
            class="rounded-2xl bg-indigo-600 px-6 py-3 font-semibold text-white transition hover:bg-indigo-500">

            Simpan Nilai

        </button>

    </div>

</div>