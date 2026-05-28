<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::livewire('/student', 'student')->name('student');
    Route::livewire('/absen', 'attendance')->name('attendance');
    Route::livewire('/rekap-absen', 'attendance-recap')->name('attendance.recap');
});

require __DIR__ . '/settings.php';
