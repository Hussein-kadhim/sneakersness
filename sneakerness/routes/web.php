<?php

use App\Http\Controllers\ContactpersoonController;
use App\Http\Controllers\EvenementController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StandController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\VerkoperController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::post('/tickets/kopen', [TicketController::class, 'kopen'])->name('tickets.kopen');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{id}/edit', [TicketController::class, 'edit'])->name('tickets.edit');
    Route::put('/tickets/{id}', [TicketController::class, 'update'])->name('tickets.update');
    Route::post('/tickets/{id}/annuleren', [TicketController::class, 'annuleren'])->name('tickets.annuleren');
    Route::get('/ticket/wijzigen/{id}', [TicketController::class, 'edit'])->name('ticket.wijzigen');
    Route::match(['post', 'put', 'patch'], '/ticket/wijzigen/{id}', [TicketController::class, 'update'])->name('ticket.wijzigen.update');
    Route::get('/verkopers', [VerkoperController::class, 'index'])->name('verkopers.index');
    Route::get('/verkopers/koppelen', [ContactpersoonController::class, 'koppelenForm'])->name('verkopers.koppelen');
    Route::post('/verkopers/koppelen', [ContactpersoonController::class, 'koppelenStore'])->name('verkopers.koppelen.store');
    Route::resource('verkopers', VerkoperController::class)->except(['index']);
    Route::get('/verkoper/wijzigen/{id}', [VerkoperController::class, 'edit'])->name('verkoper.wijzigen');
    Route::match(['post', 'put', 'patch'], '/verkoper/wijzigen/{id}', [VerkoperController::class, 'update'])->name('verkoper.wijzigen.update');
    Route::match(['post', 'delete'], '/verkoper/verwijderen/{id}', [VerkoperController::class, 'delete'])->name('verkoper.destroy');

    Route::get('/contactpersonen', [ContactpersoonController::class, 'index'])->name('contactpersonen.index');
    Route::get('/contactpersonen/create', [ContactpersoonController::class, 'create'])->name('contactpersonen.create');
    Route::post('/contactpersonen', [ContactpersoonController::class, 'store'])->name('contactpersonen.store');
    Route::get('/contactpersonen/koppelen', [ContactpersoonController::class, 'koppelenForm'])->name('contactpersonen.koppelen');
    Route::post('/contactpersonen/koppelen', [ContactpersoonController::class, 'koppelenStore'])->name('contactpersonen.koppelen.store');
    Route::get('/contactpersonen/{contactpersoon}', [ContactpersoonController::class, 'show'])->name('contactpersonen.show');
    Route::get('/contactpersonen/{contactpersoon}/edit', [ContactpersoonController::class, 'edit'])->name('contactpersonen.edit');
    Route::put('/contactpersonen/{contactpersoon}', [ContactpersoonController::class, 'update'])->name('contactpersonen.update');
    Route::patch('/contactpersonen/{contactpersoon}', [ContactpersoonController::class, 'update']);
    Route::match(['post', 'delete'], '/contactpersonen/{contactpersoon}/loskoppelen', [ContactpersoonController::class, 'loskoppelen'])->name('contactpersonen.loskoppelen');
    Route::match(['post', 'delete'], '/contactpersonen/{contactpersoon}/unlink', [ContactpersoonController::class, 'unlink'])->name('contactpersonen.unlink');
    Route::delete('/contactpersonen/{contactpersoon}', [ContactpersoonController::class, 'delete'])->name('contactpersonen.delete');
    Route::delete('/contactpersonen/{contactpersoon}/destroy', [ContactpersoonController::class, 'destroy'])->name('contactpersonen.destroy');
    Route::post('/contactpersonen/{contactpersoon}/delete', [ContactpersoonController::class, 'delete']);

    Route::get('/stands', [StandController::class, 'index'])->name('stands.index');
    Route::get('/stands/create', [StandController::class, 'create'])->name('stands.create');
    Route::post('/stands', [StandController::class, 'store'])->name('stands.store');
    Route::get('/stands/select-edit', [StandController::class, 'selectEdit'])->name('stands.select-edit');
    Route::get('/stands/{id}/edit', [StandController::class, 'edit'])->name('stands.edit');
    Route::put('/stands/{id}', [StandController::class, 'update'])->name('stands.update');
    Route::patch('/stands/{id}', [StandController::class, 'update']);
    Route::post('/stands/reserveren', [StandController::class, 'reserverenSelectie'])->name('stands.reserveren.post');
    Route::post('/stands/{id}/reserveren', [StandController::class, 'reserveren'])->name('stands.reserveren');
    Route::delete('/stands/{id}', [StandController::class, 'destroy'])->name('stands.destroy');

    // Evenementroutes zijn alleen beschikbaar voor ingelogde gebruikers; mutaties controleren de organisatorenrol.
    // GET-routes tonen het overzicht en de formulieren; POST maakt een nieuw evenement aan.
    Route::get('/events', [EvenementController::class, 'index'])->name('events.index');
    Route::get('/events/create', [EvenementController::class, 'create'])->name('events.create');
    Route::post('/events', [EvenementController::class, 'store'])->name('events.store');
    // PUT bewaart wijzigingen en DELETE verwijdert het volledige evenement.
    Route::get('/events/{event}/edit', [EvenementController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [EvenementController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EvenementController::class, 'destroy'])->name('events.destroy');

    Route::get('/dashboard', function () {
        $user = Auth::user();
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
