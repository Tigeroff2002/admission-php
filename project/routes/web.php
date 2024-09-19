<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbiturientController;


Route::get('/', [AbiturientController::class, 'index']);
Route::post('/login', [AbiturientController::class, 'loginPost']);
Route::post('/register', [AbiturientController::class, 'registerPost']);
Route::post('/logout', [AbiturientController::class, 'logoutPost']);
Route::post('/lk', [AbiturientController::class, 'getUserLKContent']);

Route::post('/directions', [AbiturientController::class, 'getAllDirections']);
Route::post('/direction', [AbiturientController::class, 'getDirectionSnapshot']);
Route::post('/directions/addNew', [AdminController::class, 'addNewDirection']);
Route::post('/directions/getEmptySnapshot', [AdminController::class, 'getDirectionEmptySnapshot']);
Route::post('/directions/fillMarks', [AdminController::class, 'fillDirectionMarks']);
Route::post('/directions/finalize', [AdminController::class, 'directionFinalize']);

Route::post('/abiturients/addInfo', [AdminController::class, 'addAbiturientLinks']);
Route::post('/abiturients/addOriginalDiplom', [AdminController::class, 'addOriginalDiplom']);
Route::post('/abiturients/all', [AdminController::class, 'getAllAbiturients']);
Route::post('/abiturients/requested', [AdminController::class, 'getRequestedAbiturients']);
Route::post('/abiturients/enrolled', [AdminController::class, 'getEnrolledAbiturients']);