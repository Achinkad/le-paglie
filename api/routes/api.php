<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\CameraController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\PetController;

/* --- [API Routes] -> Users --- */
Route::resource('users', UserController::class);
Route::get('user', [UserController::class, 'showUserLoggedIn'])->middleware('auth:api');

/* --- [API Routes] -> Camera --- */
Route::get('cameras', [CameraController::class, 'getAllCameras']);
Route::post('cameras/create', [CameraController::class, 'registerCamera']);
Route::patch('cameras/pet/{id}', [CameraController::class, 'tooglePet']);

/* --- [API Routes] -> Photo Recognition --- */
Route::get('photos', [PhotoController::class, 'getAllPhotos']);
Route::post('photos/create', [PhotoController::class, 'registerPhoto']);
Route::delete('photos/delete/{id}', [PhotoController::class, 'deletePhoto']);

/* --- [API Routes] -> Pet Identification --- */
Route::get('pets', [PetController::class, 'getPets']);
Route::post('pets/create', [PetController::class, 'registerPet']);

/* --- [API Routes] -> Alerts --- */
Route::get('alerts', [AlertController::class, 'getAllAlerts']);
Route::post('alerts/create', [AlertController::class, 'registerAlert']);

/* --- [API Routes] -> Auth --- */
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
