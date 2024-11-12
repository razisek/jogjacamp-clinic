<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\DiagnoseController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\ServiceController;
use Illuminate\Support\Facades\Route;

Route::post('/patient', [PatientController::class, 'store']);
Route::post('/diagnose', [DiagnoseController::class, 'store']);
Route::post('/service', [ServiceController::class, 'store']);

Route::post('/appointment', [AppointmentController::class, 'store']);
Route::get('/appointment/{appointment}', [AppointmentController::class, 'show']);
Route::patch('/appointment/{appointment}', [AppointmentController::class, 'update']);
