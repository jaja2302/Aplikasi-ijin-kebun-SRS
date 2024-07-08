<?php

use App\Livewire\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::get('/authUser', [Login::class, 'authUserValidation'])->name('authUserValidation');
});

Route::get('/generatePdfIzinKebun', [Login::class, 'generatePdfIzinKebun'])->name('generatePdfIzinKebun');
Route::get('/deletePdfIzinKebun', [Login::class, 'deletePdfIzinKebun'])->name('deletePdfIzinKebun');
Route::get('/checkingstatus', [Login::class, 'checkingstatus'])->name('checkingstatus');
