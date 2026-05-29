<?php

use Flux\Flux;
use Livewire\Component;
use App\Models\Students;
use App\Models\Score;

new class extends Component
{
    public string $selectedRombel = '';

    public string $mata_pelajaran = '';

    public $students = [];

    public array $scores = [];

    public array $rombelOptions = [
        '7 Efesus',
        '7 Kolose',
        '7 Filipi',
        '8 Filemon',
        '8 Smirna',
        '8 Roma',
        '9 Tesalonika',
        '9 Korintus',
        '9 Tiatira',
    ];

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

            $rataRata = round(
                (
                    (int) ($nilai['tugas'] ?? 0) +
                    (int) ($nilai['uh'] ?? 0)
                ) / 2
            );

            $nilaiAkhir = round(
                (
                    $rataRata +
                    (int) ($nilai['mid'] ?? 0) +
                    (int) ($nilai['uas'] ?? 0)
                ) / 3
            );

            Score::updateOrCreate(

                [
                    'student_id' => $student->id,
                    'mata_pelajaran' => $this->mata_pelajaran,
                ],

                [
                    'rombel' => $student->rombel,

                    'nilai_tugas' => (int) ($nilai['tugas'] ?? 0),

                    'nilai_uh' => (int) ($nilai['uh'] ?? 0),

                    'nilai_mid' => (int) ($nilai['mid'] ?? 0),

                    'nilai_uas' => (int) ($nilai['uas'] ?? 0),

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
                    <option value="{{ $rombel }}">
                        {{ $rombel }}
                    </option>
                    @endforeach

                </select>

            </div>

            {{-- Mata Pelajaran --}}
            <div>

                <label class="mb-2 block text-sm font-medium text-zinc-300">
                    Mata Pelajaran
                </label>

                <input
                    type="text"
                    wire:model="mata_pelajaran"
                    placeholder="Contoh: Informatika"
                    class="w-full rounded-2xl border border-zinc-700 bg-zinc-950 px-4 py-3 text-white">

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

                $akhir = round(
                (
                $rata +
                (int) ($nilai['mid'] ?? 0) +
                (int) ($nilai['uas'] ?? 0)
                ) / 3
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