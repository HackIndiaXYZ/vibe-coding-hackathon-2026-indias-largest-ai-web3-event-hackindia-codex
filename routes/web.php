<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnalyseController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('layout.dashboard');
})->name('dashboard');

Route::get('/signup', function () {
    return view('auth.signup');
})->name('signup');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');


Route::get('/verifyAI', function () {
    return view('layout.mainpage');
})->name('mainpage');

Route::post('/analyse', [AnalyseController::class, 'analyse'])->name('analyse');
Route::post('/signup', [AuthController::class, 'signupPost'])->name('signup.post');
Route::post('/login', [AuthController::class, 'loginPost'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::post('/activate-basic', function () {

    $user = auth()->user();

    $user->investigations_left = 100;
    $user->plan = 'BASIC';

    $user->save();

    return response()->json([
        'status' => true
    ]);
})->middleware('auth');

Route::post('/activate-pro', function () {

    $user = auth()->user();

    $user->investigations_left = 500;

    $user->plan = 'PRO';

    $user->save();

    return response()->json([
        'status' => true
    ]);
})->middleware('auth');

Route::post('/activate-unlimited', function () {

    $user = auth()->user();

    $user->investigations_left = 999999;

    $user->plan = 'UNLIMITED';

    $user->save();

    return response()->json([
        'status' => true
    ]);
})->middleware('auth');


