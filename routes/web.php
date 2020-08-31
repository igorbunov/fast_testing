<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('main');
});

Route::get('/t/{testSlug}', 'TestController@showTest');
Route::get('/r/{editSlug}', 'TestController@showResults');
Route::get('/r/{editSlug}/{resultId}', 'TestController@showOneResult');
Route::post('/get_info', 'TestController@getTestInfo');

Route::post('/finish_test', 'TestController@finishTest');
Route::post('/start_test', 'TestController@startTest');

Route::get('/new', 'TestController@startNewTest');
Route::post('/new', 'TestController@startNewTest');
Route::post('/save_new', 'TestController@saveNewTest');
Route::post('/change_status', 'TestController@editTestStatus');

Route::post('/get_answer_form', 'TestController@getAnswerForm');
Route::post('/get_question_form', 'TestController@getNewQuestionForm');
