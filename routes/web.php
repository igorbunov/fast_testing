<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('main');
});

Route::get('/edit/{slug}', 'TestController@showEditTest');
Route::post('/create_new_test', 'TestController@startNewTest');