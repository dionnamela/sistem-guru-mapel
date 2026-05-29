@php
use App\Models\Attendance;
use App\Models\AttendanceStudent;
use App\Models\Students;
use App\Models\User;

$latestActivities = Attendance::latest()
->take(5)
->get();

$totalStudents = Students::count();

$totalTeachers = User::count();

$totalMeetings = Attendance::count();

$totalRombel = Students::distinct('rombel')->count('rombel');

$totalAttendance = AttendanceStudent::count();

$totalPresent = AttendanceStudent::where('status', 'Hadir')->count();

$attendancePercentage = $totalAttendance > 0
? round(($totalPresent / $totalAttendance) * 100)
: 0;
@endphp
<x-layouts::app :title="__('Dashboard')">

    <div class="space-y-6">

        {{-- Header --}}
        <div class="rounded-3xl border border-zinc-800 bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-700 p-8 shadow-2xl">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

                <div>
                    <h1 class="text-4xl font-bold text-white">
                        Dashboard Absensi
                    </h1>

                    <p class="mt-3 max-w-2xl text-indigo-100">
                        Selamat datang di sistem absensi sekolah.
                        Pantau data kehadiran siswa, statistik absensi,
                        dan aktivitas guru dengan mudah.
                    </p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">

                    {{-- Total Siswa --}}
                    <div class="rounded-3xl border border-white/10 bg-white/10 p-5 backdrop-blur-xl shadow-lg">

                        <div class="flex items-center justify-between">

                            <div>
                                <p class="text-sm font-medium text-indigo-100">
                                    Total Siswa
                                </p>

                                <h2 class="mt-3 text-4xl font-bold tracking-tight text-white">
                                    {{ $totalStudents }}
                                </h2>

                                <p class="mt-1 text-xs text-indigo-200">
                                    Siswa terdaftar
                                </p>
                            </div>

                            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/10 text-3xl shadow-inner">
                                🎓
                            </div>

                        </div>
                    </div>

                    {{-- Total Guru --}}
                    <div class="rounded-3xl border border-white/10 bg-white/10 p-5 backdrop-blur-xl shadow-lg">

                        <div class="flex items-center justify-between">

                            <div>
                                <p class="text-sm font-medium text-indigo-100">
                                    Total Guru
                                </p>

                                <h2 class="mt-3 text-4xl font-bold tracking-tight text-white">
                                    {{ $totalTeachers }}
                                </h2>

                                <p class="mt-1 text-xs text-indigo-200">
                                    Guru aktif
                                </p>
                            </div>

                            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/10 text-3xl shadow-inner">
                                👨‍🏫
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Statistik --}}
        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">

            {{-- Card --}}
            <div class="rounded-3xl border border-zinc-800 bg-zinc-900 p-6 shadow-xl transition hover:-translate-y-1 hover:shadow-indigo-500/10">

                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm text-zinc-400">
                            Hadir Hari Ini
                        </p>

                        <h2 class="mt-3 text-4xl font-bold text-green-400">
                            210
                        </h2>
                    </div>

                    <div class="rounded-2xl bg-green-500/10 p-4 text-3xl text-green-400">
                        ✅
                    </div>
                </div>
            </div>

            {{-- Card --}}
            <div class="rounded-3xl border border-zinc-800 bg-zinc-900 p-6 shadow-xl transition hover:-translate-y-1 hover:shadow-yellow-500/10">

                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm text-zinc-400">
                            Izin
                        </p>

                        <h2 class="mt-3 text-4xl font-bold text-yellow-400">
                            15
                        </h2>
                    </div>

                    <div class="rounded-2xl bg-yellow-500/10 p-4 text-3xl text-yellow-400">
                        📄
                    </div>
                </div>
            </div>

            {{-- Card --}}
            <div class="rounded-3xl border border-zinc-800 bg-zinc-900 p-6 shadow-xl transition hover:-translate-y-1 hover:shadow-orange-500/10">

                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm text-zinc-400">
                            Sakit
                        </p>

                        <h2 class="mt-3 text-4xl font-bold text-orange-400">
                            8
                        </h2>
                    </div>

                    <div class="rounded-2xl bg-orange-500/10 p-4 text-3xl text-orange-400">
                        🤒
                    </div>
                </div>
            </div>

            {{-- Card --}}
            <div class="rounded-3xl border border-zinc-800 bg-zinc-900 p-6 shadow-xl transition hover:-translate-y-1 hover:shadow-red-500/10">

                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm text-zinc-400">
                            Alpha
                        </p>

                        <h2 class="mt-3 text-4xl font-bold text-red-400">
                            12
                        </h2>
                    </div>

                    <div class="rounded-2xl bg-red-500/10 p-4 text-3xl text-red-400">
                        ❌
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="grid gap-6 lg:grid-cols-3">

            {{-- Aktivitas --}}
            <div class="lg:col-span-2 rounded-3xl border border-zinc-800 bg-zinc-900 p-6 shadow-xl">

                <div class="mb-6 flex items-center justify-between">

                    <div>
                        <h2 class="text-2xl font-bold text-white">
                            Aktivitas Terbaru
                        </h2>

                        <p class="text-sm text-zinc-400">
                            Aktivitas absensi guru terbaru.
                        </p>
                    </div>

                    <button class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700">
                        Lihat Semua
                    </button>
                </div>

                <div class="space-y-4">

                    @forelse ($latestActivities as $activity)

                    <div class="flex items-center justify-between rounded-2xl border border-zinc-800 bg-zinc-950 p-4 transition hover:bg-zinc-900">

                        <div>
                            <h3 class="font-semibold text-white">
                                Absensi {{ $activity->rombel }}
                            </h3>

                            <p class="text-sm text-zinc-400">
                                Oleh {{ $activity->teacher_name }}
                            </p>
                        </div>

                        <div class="text-right">
                            <p class="text-sm text-zinc-500">
                                {{ \Carbon\Carbon::parse($activity->tanggal)->format('d M Y') }}
                            </p>

                            <p class="text-xs text-zinc-600">
                                {{ $activity->created_at->diffForHumans() }}
                            </p>
                        </div>

                    </div>

                    @empty

                    <div class="rounded-2xl border border-zinc-800 bg-zinc-950 p-6 text-center text-zinc-500">
                        Belum ada aktivitas absensi.
                    </div>

                    @endforelse

                </div>
            </div>

            {{-- Informasi --}}
            <div class="rounded-3xl border border-zinc-800 bg-zinc-900 p-6 shadow-xl">

                <h2 class="text-2xl font-bold text-white">
                    Informasi
                </h2>

                <p class="mt-1 text-sm text-zinc-400">
                    Ringkasan sistem sekolah.
                </p>

                <div class="mt-6 space-y-5">

                    {{-- Tingkat Kehadiran --}}
                    <div class="rounded-3xl border border-zinc-800 bg-zinc-950 p-5 shadow-lg">

                        <div class="flex items-center justify-between">

                            <div>
                                <p class="text-sm text-zinc-400">
                                    Tingkat Kehadiran
                                </p>

                                <h3 class="mt-2 text-4xl font-bold text-green-400">
                                    {{ $attendancePercentage }}%
                                </h3>

                                <p class="mt-1 text-xs text-zinc-500">
                                    Persentase kehadiran siswa
                                </p>
                            </div>

                            <div class="rounded-2xl bg-green-500/10 p-4 text-3xl text-green-400">
                                📈
                            </div>

                        </div>
                    </div>

                    {{-- Rombel Aktif --}}
                    <div class="rounded-3xl border border-zinc-800 bg-zinc-950 p-5 shadow-lg">

                        <div class="flex items-center justify-between">

                            <div>
                                <p class="text-sm text-zinc-400">
                                    Rombel Aktif
                                </p>

                                <h3 class="mt-2 text-4xl font-bold text-indigo-400">
                                    {{ $totalRombel }}
                                </h3>

                                <p class="mt-1 text-xs text-zinc-500">
                                    Total rombel tersedia
                                </p>
                            </div>

                            <div class="rounded-2xl bg-indigo-500/10 p-4 text-3xl text-indigo-400">
                                🏫
                            </div>

                        </div>
                    </div>

                    {{-- Total Pertemuan --}}
                    <div class="rounded-3xl border border-zinc-800 bg-zinc-950 p-5 shadow-lg">

                        <div class="flex items-center justify-between">

                            <div>
                                <p class="text-sm text-zinc-400">
                                    Total Pertemuan
                                </p>

                                <h3 class="mt-2 text-4xl font-bold text-purple-400">
                                    {{ $totalMeetings }}
                                </h3>

                                <p class="mt-1 text-xs text-zinc-500">
                                    Seluruh sesi absensi
                                </p>
                            </div>

                            <div class="rounded-2xl bg-purple-500/10 p-4 text-3xl text-purple-400">
                                📚
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

</x-layouts::app>