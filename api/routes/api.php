<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\CameraController;

/* --- [API Routes] -> Users --- */
Route::resource('users', UserController::class);
Route::get('user', [UserController::class, 'showUserLoggedIn'])->middleware('auth:api');

/* --- [API Routes] -> Camera --- */
Route::get('cameras', [CameraController::class, 'getAllCameras']);
Route::post('cameras/create', [CameraController::class, 'registerCamera']);

/* --- [API Routes] -> Camera --- */
Route::get('alerts', [AlertController::class, 'getAllAlerts']);
Route::post('alerts/create', [AlertController::class, 'registerAlert']);

/* --- [API Routes] -> Auth --- */
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
