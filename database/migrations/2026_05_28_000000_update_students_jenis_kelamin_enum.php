<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('students')
            ->where('jenis_kelamin', 'Laki-laki')
            ->update(['jenis_kelamin' => 'L']);

        DB::table('students')
            ->where('jenis_kelamin', 'Perempuan')
            ->update(['jenis_kelamin' => 'P']);

        Schema::table('students', function (Blueprint $table) {
            $table->enum('jenis_kelamin', ['L', 'P'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('students')
            ->where('jenis_kelamin', 'L')
            ->update(['jenis_kelamin' => 'Laki-laki']);

        DB::table('students')
            ->where('jenis_kelamin', 'P')
            ->update(['jenis_kelamin' => 'Perempuan']);

        Schema::table('students', function (Blueprint $table) {
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->change();
        });
    }
};
