<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::view('dashboard', 'dashboard')
        ->name('dashboard');

    Route::livewire('/student', 'student')
        ->name('student');

    Route::livewire('/absen', 'attendance')
        ->name('attendance');

    Route::livewire('/rekap-absen', 'attendance-recap')
        ->name('attendance.recap');

    Route::livewire('/scores', 'scores')
        ->name('scores');
});

require __DIR__ . '/settings.php';
