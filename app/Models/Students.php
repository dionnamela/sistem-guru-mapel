<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Students extends Model
{
    protected $table = 'students';

    protected $fillable = [
        'nama',
        'nisn',
        'rombel',
        'jenis_kelamin',
    ];

    public function scores()
    {
        return $this->hasMany(Score::class);
    }
}
