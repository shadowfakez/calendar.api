<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CalendarController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum')->name('me');

Route::apiResource('/calendar', CalendarController::class)
    ->except('show', 'store')
    ->middleware('auth:sanctum');;
Route::post('/calendar', [CalendarController::class, 'store'])->name('calendar.store');
