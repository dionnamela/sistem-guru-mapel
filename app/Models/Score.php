<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    protected $fillable = [
        'student_id',
        'mata_pelajaran',
        'rombel',
        'nilai_tugas',
        'nilai_uh',
        'nilai_mid',
        'nilai_uas',
        'nilai_absen',
        'rata_rata',
        'nilai_akhir',
    ];

    public function student()
    {
        return $this->belongsTo(Students::class);
    }
}
