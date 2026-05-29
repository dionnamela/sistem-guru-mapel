<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    protected $fillable = [
        'student_id',
        'rombel',
        'mata_pelajaran',
        'nilai_tugas',
        'nilai_uh',
        'nilai_mid',
        'rata_rata',
        'nilai_akhir',
    ];

    public function student()
    {
        return $this->belongsTo(Students::class);
    }
}