<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'App\Http\Controllers\AuthController@index');
Route::post('/login', 'App\Http\Controllers\AuthController@login')->name('login');
Route::post('/signup', 'App\Http\Controllers\AuthController@signup')->name('signup');

Route::middleware('guest')->group(function () {
    Route::get('/home', 'App\Http\Controllers\HomeController@index')->middleware('auth');
});
