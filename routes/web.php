<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/getMotivationalMessage', 'GPTController@getMotivationalMessage');
Route::post('/getHelp/{msg}', 'GPTController@getHelp');
