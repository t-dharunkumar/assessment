<?php

use App\Http\Controllers\AssessmentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->post('/violations', [AssessmentController::class, 'violations']);
