<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbiturientController;


Route::get('/', [AbiturientController::class, 'index'])->name('raw_user.index');
Route::get('/login', [AbiturientController::class, 'loginPost'])->name('raw_user.index');
