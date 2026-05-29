<?php

use Flux\Flux;
use Livewire\Component;
use App\Models\Attendance;
use App\Models\AttendanceStudent;
use App\Models\Students;

new class extends Component {
    public string $selectedRombel = '';
    public string $selectedMonthYear = '';
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
    public array $monthOptions = [];
    public array $records = [];
    public array $statusSummary = [];
    public array $students = [];
    public array $studentSummaries = [];

    public function mount(): void
    {
        $this->loadMonthOptions();
        $this->loadRecords();
    }

    public function updatedSelectedRombel(): void
    {
        $this->loadRecords();
    }

    public function updatedSelectedMonthYear(): void
    {
        $this->loadRecords();
    }

    private function loadMonthOptions(): void
    {
        $this->monthOptions = Attendance::selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as month")
            ->distinct()
            ->orderBy('month', 'desc')
            ->pluck('month')
            ->toArray();

        if ($this->selectedMonthYear === '' && count($this->monthOptions) > 0) {
            $this->selectedMonthYear = $this->monthOptions[0];
        }
    }

    public function loadRecords(): void
    {
        if ($this->selectedRombel === '' || $this->selectedMonthYear === '') {
            $this->records = [];
            $this->statusSummary = [];
            $this->students = [];
            $this->studentSummaries = [];
            return;
        }

        $isAll = $this->selectedMonthYear === 'all';

        $year = null;
        $month = null;

        if (! $isAll) {
            [$year, $month] = explode('-', $this->selectedMonthYear);
        }

        $query = Attendance::with(['students.student'])
            ->where('rombel', $this->selectedRombel);

        if (! $isAll) {
            $query->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month);
        }

        $records = $query
            ->orderBy('tanggal', 'desc')
            ->get();

        $this->records = $records->toArray();

        $this->statusSummary = AttendanceStudent::selectRaw('status, count(*) as total')
            ->whereHas('attendance', function ($query) use ($isAll, $year, $month) {

                $query->where('rombel', $this->selectedRombel);

                if (! $isAll) {
                    $query->whereYear('tanggal', $year)
                        ->whereMonth('tanggal', $month);
                }
            })
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $this->students = Students::where('rombel', $this->selectedRombel)
            ->orderBy('nama')
            ->get()
            ->toArray();

        $this->studentSummaries = [];
        foreach ($this->students as $student) {
            $this->studentSummaries[$student['id']] = [
                'nama' => $student['nama'],
                'hadir' => 0,
                'izin' => 0,
                'sakit' => 0,
                'alpha' => 0,
                'total' => 0,
            ];
        }

        foreach ($this->records as $record) {
            foreach ($record['students'] as $studentRow) {
                $studentId = $studentRow['student_id'];
                if (! array_key_exists($studentId, $this->studentSummaries)) {
                    continue;
                }

                $this->studentSummaries[$studentId]['total'] += 1;
                if ($studentRow['status'] === 'Hadir') {
                    $this->studentSummaries[$studentId]['hadir'] += 1;
                } elseif ($studentRow['status'] === 'Izin') {
                    $this->studentSummaries[$studentId]['izin'] += 1;
                } elseif ($studentRow['status'] === 'Sakit') {
                    $this->studentSummaries[$studentId]['sakit'] += 1;
                } elseif ($studentRow['status'] === 'Alpha') {
                    $this->studentSummaries[$studentId]['alpha'] += 1;
                }
            }
        }
    }
};
?>

<div class="p-6 bg-zinc-900 rounded-2xl shadow-2xl border border-zinc-800">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-white">
                Rekap Absen
            </h1>
            <p class="text-zinc-400 mt-1">
                Pilih rombel untuk melihat semua sesi absensi dan ringkasannya.
            </p>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">

        {{-- Filter Card --}}
        <div class="lg:col-span-2 rounded-2xl border border-zinc-800 bg-zinc-950 p-5">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-lg font-semibold text-white">
                        Filter Rekap
                    </h2>
                    <p class="text-sm text-zinc-400">
                        Pilih rombel dan bulan untuk melihat data absensi.
                    </p>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">

                {{-- Rombel --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-zinc-300">
                        Rombel
                    </label>

                    <select
                        wire:model="selectedRombel"
                        wire:change="loadRecords"
                        class="w-full rounded-xl border border-zinc-700 bg-zinc-900 px-4 py-3 text-white transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">

                        <option value="">Pilih Rombel</option>

                        @foreach ($rombelOptions as $rombelOption)
                        <option value="{{ $rombelOption }}">
                            {{ $rombelOption }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Bulan --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-zinc-300">
                        Bulan
                    </label>
                    <select
                        wire:model="selectedMonthYear"
                        wire:change="loadRecords"
                        class="w-full rounded-xl border border-zinc-700 bg-zinc-900 px-4 py-3 text-white transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">

                        <option value="">Pilih Bulan</option>

                        <option value="all">
                            Semua Pertemuan
                        </option>

                        @foreach ($monthOptions as $monthOption)
                        <option value="{{ $monthOption }}">
                            {{ $monthOption }}
                        </option>
                        @endforeach
                    </select>
                </div>

            </div>
        </div>

        {{-- Statistik --}}
        <div class="grid gap-4 sm:grid-cols-3 lg:grid-cols-1">

            {{-- Total Sesi --}}
            <div class="rounded-2xl border border-zinc-800 bg-gradient-to-br from-zinc-900 to-zinc-950 p-5 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-400">
                            Total Sesi
                        </p>

                        <h3 class="mt-2 text-3xl font-bold text-white">
                            {{ count($records) }}
                        </h3>
                    </div>

                    <div class="rounded-xl bg-indigo-500/10 p-3 text-indigo-400">
                        📅
                    </div>
                </div>
            </div>

            {{-- Hadir --}}
            <div class="rounded-2xl border border-zinc-800 bg-gradient-to-br from-zinc-900 to-zinc-950 p-5 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-400">
                            Hadir
                        </p>

                        <h3 class="mt-2 text-3xl font-bold text-green-400">
                            {{ $statusSummary['Hadir'] ?? 0 }}
                        </h3>
                    </div>

                    <div class="rounded-xl bg-green-500/10 p-3 text-green-400">
                        ✅
                    </div>
                </div>
            </div>

            {{-- Tidak Hadir --}}
            <div class="rounded-2xl border border-zinc-800 bg-gradient-to-br from-zinc-900 to-zinc-950 p-5 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-400">
                            Izin / Alpha
                        </p>

                        <h3 class="mt-2 text-3xl font-bold text-red-400">
                            {{ ($statusSummary['Izin'] ?? 0) + ($statusSummary['Alpha'] ?? 0) }}
                        </h3>
                    </div>

                    <div class="rounded-xl bg-red-500/10 p-3 text-red-400">
                        ⚠️
                    </div>
                </div>
            </div>
            {{-- Sakit --}}
            <div class="rounded-2xl border border-zinc-800 bg-gradient-to-br from-zinc-900 to-zinc-950 p-5 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-400">
                            Sakit
                        </p>

                        <h3 class="mt-2 text-3xl font-bold text-yellow-400">
                            {{ $statusSummary['Sakit'] ?? 0 }}
                        </h3>
                    </div>

                    <div class="rounded-xl bg-yellow-500/10 p-3 text-yellow-400">
                        🤒
                    </div>
                </div>
            </div>

        </div>
    </div>
    <br>
    <div class="overflow-x-auto rounded-xl border border-zinc-800">
        <table class="min-w-full">
            <thead class="bg-zinc-800 text-zinc-300">
                <tr>
                    <th class="px-5 py-4 text-left text-sm font-semibold">Tanggal</th>
                    <th class="px-5 py-4 text-left text-sm font-semibold">Guru</th>
                    <th class="px-5 py-4 text-left text-sm font-semibold">Jumlah Siswa</th>
                    <th class="px-5 py-4 text-left text-sm font-semibold">Rincian Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800">
                @if ($selectedRombel === '')
                <tr>
                    <td colspan="4" class="px-5 py-10 text-center text-zinc-400">
                        Pilih rombel untuk menampilkan rekap absen.
                    </td>
                </tr>
                @elseif (count($records) === 0)
                <tr>
                    <td colspan="4" class="px-5 py-10 text-center text-zinc-400">
                        Belum ada rekap absensi untuk rombel ini.
                    </td>
                </tr>
                @else
                @foreach ($records as $record)
                <tr class="hover:bg-zinc-800/60 transition duration-200">
                    <td class="px-5 py-4 text-zinc-300">{{ $record['tanggal'] }}</td>
                    <td class="px-5 py-4 text-zinc-300">{{ $record['teacher_name'] }}</td>
                    <td class="px-5 py-4 text-zinc-300">{{ count($record['students']) }}</td>
                    <td class="px-5 py-4 text-zinc-300">
                        @php
                        $counts = ['Hadir' => 0, 'Izin' => 0, 'Alpha' => 0];
                        foreach ($record['students'] as $studentRow) {
                        $counts[$studentRow['status']] = ($counts[$studentRow['status']] ?? 0) + 1;
                        }
                        @endphp
                        Hadir: {{ $counts['Hadir'] ?? 0 }},
                        Izin: {{ $counts['Izin'] ?? 0 }},
                        Sakit: {{ $counts['Sakit'] ?? 0 }},
                        Alpha: {{ $counts['Alpha'] ?? 0 }}
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <div class="mt-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-2xl font-semibold text-white">Siswa di {{ $selectedRombel ?: 'Rombel' }}</h2>
                <p class="text-zinc-400">Rekap kehadiran siswa untuk bulan yang dipilih.</p>
            </div>
        </div>
        @php
        $isAllRekap = $selectedMonthYear === 'all';
        @endphp
        <div class="overflow-x-auto rounded-xl border border-zinc-800">
            <table class="min-w-full">
                <thead class="bg-zinc-800 text-zinc-300">
                    <tr>
                        <th class="px-5 py-4 text-left text-sm font-semibold">No</th>
                        <th class="px-5 py-4 text-left text-sm font-semibold">Nama</th>
                        <th class="px-5 py-4 text-left text-sm font-semibold">Total Sesi</th>
                        <th class="px-5 py-4 text-left text-sm font-semibold">Hadir</th>
                        <th class="px-5 py-4 text-left text-sm font-semibold">Izin</th>
                        <th class="px-5 py-4 text-left text-sm font-semibold">Sakit</th>
                        <th class="px-5 py-4 text-left text-sm font-semibold">Alpha</th>
                        @if ($isAllRekap)
                        <th class="px-5 py-4 text-left text-sm font-semibold">
                            Nilai Kehadiran
                        </th>
                        @endif
                    </tr>
                </thead>

                <tbody class="divide-y divide-zinc-800">
                    @if ($selectedRombel === '' || $selectedMonthYear === '')
                    <tr>
                        <td colspan="8" class="px-5 py-10 text-center text-zinc-400">
                            Pilih rombel dan bulan untuk melihat rekap siswa.
                        </td>
                    </tr>
                    @elseif (count($students) === 0)
                    <tr>
                        <td colspan="8" class="px-5 py-10 text-center text-zinc-400">
                            Tidak ada siswa di rombel ini.
                        </td>
                    </tr>
                    @else
                    @foreach ($students as $index => $student)
                    @php
                    $summary = $studentSummaries[$student['id']] ?? [
                    'hadir' => 0,
                    'izin' => 0,
                    'sakit' => 0,
                    'alpha' => 0,
                    'total' => 0,
                    ];

                    $nilaiKehadiran = $summary['total'] > 0
                    ? round(($summary['hadir'] / $summary['total']) * 100)
                    : 0;
                    @endphp

                    <tr class="hover:bg-zinc-800/60 transition duration-200">
                        <td class="px-5 py-4 text-zinc-300">
                            {{ $index + 1 }}
                        </td>

                        <td class="px-5 py-4 text-zinc-300">
                            {{ $student['nama'] }}
                        </td>

                        <td class="px-5 py-4 text-zinc-300">
                            {{ $summary['total'] }}
                        </td>

                        <td class="px-5 py-4 text-zinc-300">
                            {{ $summary['hadir'] }}
                        </td>

                        <td class="px-5 py-4 text-zinc-300">
                            {{ $summary['izin'] }}
                        </td>

                        <td class="px-5 py-4 text-zinc-300">
                            {{ $summary['sakit'] }}
                        </td>

                        <td class="px-5 py-4 text-zinc-300">
                            {{ $summary['alpha'] }}
                        </td>

                        @if ($isAllRekap)
                        <td class="px-5 py-4">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $nilaiKehadiran >= 90 ? 'bg-green-500/20 text-green-400' : '' }} {{ $nilaiKehadiran >= 75 && $nilaiKehadiran < 90 ? 'bg-yellow-500/20 text-yellow-400' : '' }} {{ $nilaiKehadiran < 75 ? 'bg-red-500/20 text-red-400' : '' }}">
                                {{ $nilaiKehadiran }}%
                            </span>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>