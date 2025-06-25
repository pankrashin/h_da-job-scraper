<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobController;

// When a user visits '/', call the 'index' method on JobController
Route::get('/', [JobController::class, 'index']);