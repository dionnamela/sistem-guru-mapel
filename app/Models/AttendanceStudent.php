<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceStudent extends Model
{
    protected $table = 'attendance_students';

    protected $fillable = [
        'attendance_id',
        'student_id',
        'status',
        'keterangan',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function student()
    {
        return $this->belongsTo(Students::class, 'student_id');
    }
}
