<?php

use Illuminate\Support\Facades\Route;

Route::get('/signin', function () {
    return view('signin');
});

Route::get('/signup', function () {
    return view('signup');
});
