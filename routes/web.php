<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;

Route::get('/'       , [AuthController::class, 'index']);
Route::post('/login' , [AuthController::class, 'login'])->name('login');
Route::post('/signup', [AuthController::class, 'signup'])->name('signup');

Route::middleware('auth')->group(function () {
    Route::get('/home'         , [HomeController::class, 'index'])->name('home');
    Route::get('/user/details' , [HomeController::class, 'getUserDetails'])->name('user.details');
    Route::get('/user/contacts', [HomeController::class, 'getContacts'])->name('user.contacts');
    Route::post('/logout'      , [AuthController::class, 'logout'])->name('logout');
    Route::post('/add-contact' , [HomeController::class, 'addContact'])->name('add.contact');
    Route::post('/send-message', [HomeController::class, 'sendMessage'])->name('send.message');
    Route::post('/get-messages', [HomeController::class, 'getMessages'])->name('get.messages');

});
