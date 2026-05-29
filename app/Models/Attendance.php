<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendances';

    protected $fillable = [
        'rombel',
        'mapel',
        'teacher_name',
        'tanggal',
    ];

    public function students()
    {
        return $this->hasMany(AttendanceStudent::class);
    }
}
