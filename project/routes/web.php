<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbiturientController;


Route::get('/', [AbiturientController::class, 'index']);
Route::get('/login', [AbiturientController::class, 'loginPost']);
Route::post('/register', [AbiturientController::class, 'registerPost']);
Route::post('/logout', [AbiturientController::class, 'logoutPost']);
Route::post('/lk', [AbiturientController::class, 'getUserLKContent']);
Route::post('/directions', [AbiturientController::class, 'getAllDirections']);
Route::post('/directions/:id', [AbiturientController::class, 'getDirectionSnapshot']);

Route::post('/abiturients', [AdminController::class, 'getAllAbiturients']);
Route::post('/directions/empty/:id', [AdminController::class, 'getDirectionEmptySnapshot']);
Route::post('/directions/finalize/:id', [AdminController::class, 'directionFinalize']);