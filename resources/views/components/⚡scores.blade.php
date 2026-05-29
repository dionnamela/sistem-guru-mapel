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
            ];
        }
    }

    public function save()
    {
        foreach ($this->students as $student) {

            $nilai = $this->scores[$student->id];

            $rataRata = round(
                (
                    $nilai['tugas'] +
                    $nilai['uh']
                ) / 2
            );

            $nilaiAkhir = round(
                (
                    $rataRata +
                    $nilai['mid']
                ) / 2
            );

            Score::updateOrCreate(

                [
                    'student_id' => $student->id,
                    'mata_pelajaran' => $this->mata_pelajaran,
                ],

                [
                    'rombel' => $student->rombel,

                    'nilai_tugas' => $nilai['tugas'],

                    'nilai_uh' => $nilai['uh'],

                    'nilai_mid' => $nilai['mid'],

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

    <div class="rounded-3xl border border-zinc-800 bg-zinc-900 p-6">

        <h1 class="text-3xl font-bold text-white">
            Input Nilai Siswa
        </h1>

        <p class="mt-2 text-zinc-400">
            Input nilai tugas, UH, dan MID siswa.
        </p>

    </div>

</div>