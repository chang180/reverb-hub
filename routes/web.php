<?php

use App\Http\Controllers\LocaleController;
use App\Livewire\Applications\Index as ApplicationsIndex;
use App\Livewire\Applications\Show as ApplicationShow;
use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::post('locale/{locale}', [LocaleController::class, 'update'])->name('locale.update');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('dashboard', Dashboard::class)->name('dashboard');
    Route::livewire('applications', ApplicationsIndex::class)->name('applications.index');
    Route::livewire('applications/{application}', ApplicationShow::class)->name('applications.show');
});

require __DIR__.'/settings.php';
