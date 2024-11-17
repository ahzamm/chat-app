<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;

Route::get('/'       , [AuthController::class, 'index']);
Route::post('/login' , [AuthController::class, 'login'])->name('login');
Route::post('/signup', [AuthController::class, 'signup'])->name('signup');

Route::middleware('auth')->group(function () {
    Route::get('/home'                           , [HomeController::class, 'index'])->name('home');
    Route::get('/user/details'                   , [HomeController::class, 'getUserDetails'])->name('user.details');
    Route::get('/user/contacts'                  , [HomeController::class, 'getContacts'])->name('user.contacts');
    Route::get('/user/contacts-and-groups'       , [HomeController::class, 'getContactsAndGroups'])->name('user.contacts.and.groups');
    Route::post('/logout'                        , [AuthController::class, 'logout'])->name('logout');
    Route::post('/add-contact'                   , [HomeController::class, 'addContact'])->name('add.contact');
    Route::post('/send-message'                  , [HomeController::class, 'sendMessage'])->name('send.message');
    Route::post('/get-messages'                  , [HomeController::class, 'getMessages'])->name('get.messages');
    Route::post('/create-group'                  , [HomeController::class, 'createGroup'])->name('create.group');
    Route::post('/get-group-messages'            , [HomeController::class, 'getGroupMessages'])->name('get.group.messages');
    Route::post('/notifications/mark-all-as-read', [HomeController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::post('/send-group-message'            , [HomeController::class, 'sendGroupMessage'])->name('send.group.message');

});
