<?php

use App\Http\Controllers\ContactpersoonController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VerkoperController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/verkopers', [VerkoperController::class, 'index'])->name('verkopers.index');
Route::resource('verkopers', VerkoperController::class)->except(['index']);
Route::get('/contactpersonen', [ContactpersoonController::class, 'index'])->name('contactpersonen.index');


Route::get('/dashboard', function () {
    return redirect()->route('verkopers.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
