<?php

use App\Livewire\Users\UsersIndex;
use App\Livewire\Settings\SettingsIndex;
use App\Livewire\Groups\GroupsIndex;
use App\Livewire\Groups\GroupShow;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/usuarios', UsersIndex::class)->middleware('section:usuarios')->name('users.index');
    Route::get('/grupos', GroupsIndex::class)->middleware('section:configuracion,alumnos')->name('groups.index');
    Route::get('/grupos/{group}', GroupShow::class)->middleware('section:configuracion,alumnos')->name('groups.show');
    Route::get('/configuracion', SettingsIndex::class)->middleware('section:configuracion')->name('settings.index');
});
