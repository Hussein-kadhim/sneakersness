<?php

use App\Http\Controllers\ContactpersoonController;
use App\Http\Controllers\EvenementController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StandController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\VerkoperController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/verkopers', [VerkoperController::class, 'index'])->name('verkopers.index');
    Route::get('/verkopers/koppelen', [ContactpersoonController::class, 'koppelenForm'])->name('verkopers.koppelen');
    Route::post('/verkopers/koppelen', [ContactpersoonController::class, 'koppelenStore'])->name('verkopers.koppelen.store');
    Route::resource('verkopers', VerkoperController::class)->except(['index']);
    
    Route::get('/contactpersonen', [ContactpersoonController::class, 'index'])->name('contactpersonen.index');
    Route::get('/contactpersonen/create', [ContactpersoonController::class, 'create'])->name('contactpersonen.create');
    Route::post('/contactpersonen', [ContactpersoonController::class, 'store'])->name('contactpersonen.store');
    Route::get('/contactpersonen/koppelen', [ContactpersoonController::class, 'koppelenForm'])->name('contactpersonen.koppelen');
    Route::post('/contactpersonen/koppelen', [ContactpersoonController::class, 'koppelenStore'])->name('contactpersonen.koppelen.store');
    
    Route::get('/stands', [StandController::class, 'index'])->name('stands.index');
    Route::get('/stands/create', [StandController::class, 'create'])->name('stands.create');
    Route::post('/stands', [StandController::class, 'store'])->name('stands.store');
    Route::post('/stands/reserveren', [StandController::class, 'reserverenSelectie'])->name('stands.reserveren.post');
    Route::post('/stands/{id}/reserveren', [StandController::class, 'reserveren'])->name('stands.reserveren');
    Route::get('/events', [EvenementController::class, 'index'])->name('events.index');
    Route::get('/events/create', [EvenementController::class, 'create'])->name('events.create');
    Route::post('/events', [EvenementController::class, 'store'])->name('events.store');

    Route::get('/dashboard', function () {
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user && $user->role === 'verkoper') {
            return redirect()->route('stands.index');
        }
        if ($user && $user->role === 'bezoeker') {
            return redirect()->route('events.index');
        }
        return redirect()->route('verkopers.index');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
