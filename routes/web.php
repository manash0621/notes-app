<?php

use App\Http\Controllers\NoteController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', [NoteController::class,'index'])->name('dashboard');
    Route::post('/notes', [NoteController::class,'store'])->name('notes.store');
    Route::post('/notes/{note}', [NoteController::class,'update']);
    Route::post('/notes/{note}/delete', [NoteController::class,'destroy']); 
    Route::put('/notes/{note}', [NoteController::class,'update'])->name('notes.update');
    Route::delete('/notes/{note}', [NoteController::class,'destroy'])->name('notes.destroy');
});

require __DIR__ . '/auth.php';
