<?php

use Illuminate\Support\Facades\Route;
use Presentation\User\Controllers\Auth\LoginController;
use Presentation\User\Controllers\Auth\LogoutController;
use Presentation\User\Controllers\Auth\ProfileController;
use Presentation\User\Controllers\Auth\RefreshTokenController;
use Presentation\User\Controllers\Auth\RegisterController;

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);
Route::post('/refresh', [RefreshTokenController::class, 'refresh']);
Route::post('/logout', [LogoutController::class, 'logout']);

Route::middleware('auth.jwt')->get('/profile', [ProfileController::class, 'profile']);
