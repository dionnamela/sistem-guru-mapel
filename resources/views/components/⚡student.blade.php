<?php

use Livewire\Component;
use App\Models\Students;

new class extends Component {
    public $students = [];

    public function mount()
    {
        $this->students = Students::all();
    }
};
?>

<div class="p-6 bg-zinc-900 rounded-2xl shadow-2xl border border-zinc-800">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-white">
                Data Siswa
            </h1>

            <p class="text-zinc-400 mt-1">
                Daftar seluruh siswa
            </p>
        </div>

        <div class="px-4 py-2 bg-indigo-500/20 text-indigo-400 rounded-xl text-sm font-medium">
            Total: {{ count($students) }} siswa
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
                                    class="px-4 py-2 rounded-xl bg-zinc-700 hover:bg-zinc-600 text-white text-sm font-medium transition duration-200 shadow-lg">

                                    Edit

                                </button>

                                <button
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

</div>
