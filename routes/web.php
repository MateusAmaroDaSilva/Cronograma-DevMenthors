<?php

use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\SubstituteController;
use Illuminate\Support\Facades\Route;

// Web routes
Route::get('/', [ScheduleController::class, 'index'])->name('schedule.index');

// API routes for AJAX/Fetch
Route::post('/schedule/change-month', [ScheduleController::class, 'changeMonth']);
Route::post('/schedule/generate', [ScheduleController::class, 'generate']);
Route::post('/schedule/clear', [ScheduleController::class, 'clear']);
Route::post('/schedule/assign', [ScheduleController::class, 'assign']);
Route::post('/schedule/remove', [ScheduleController::class, 'remove']);
Route::post('/schedule/lesson-name', [ScheduleController::class, 'updateLessonName']);

Route::post('/substitutes/assign', [SubstituteController::class, 'assign']);
Route::post('/substitutes/remove', [SubstituteController::class, 'remove']);

Route::post('/people', [PersonController::class, 'store']);
Route::delete('/people/{person}', [PersonController::class, 'destroy']);
Route::get('/people', [PersonController::class, 'all']);
