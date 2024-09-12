<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbiturientController;


Route::get('/', [AbiturientController::class, 'index']);
Route::get('/login', [AbiturientController::class, 'loginPost']);
Route::post('/register', [AbiturientController::class, 'registerPost']);
