<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('main');
});

Route::get('/test', 'TestController@finishExpiredTests');
Route::get('/e/{editSlug}', 'TestController@showEditTest');
Route::get('/t/{testSlug}', 'TestController@showTest');
Route::get('/r/{editSlug}', 'TestController@showResults');
Route::get('/r/{editSlug}/{resultId}', 'TestController@showOneResult');
Route::post('/get_info', 'TestController@getTestInfo');

Route::post('/finish_test', 'TestController@finishTest');
Route::post('/start_test', 'TestController@startTest');

Route::post('/create_new_test', 'TestController@startNewTest');
Route::get('/create_new_test', 'TestController@startNewTest');
Route::post('/save_test', 'TestController@editTest');
Route::post('/change_status', 'TestController@editTestStatus');

Route::post('/get_answer_form', 'TestController@getAnswerForm');
Route::post('/get_question_form', 'TestController@getNewQuestionForm');

Route::post('/delete_question', 'TestController@deleteQuestion');
Route::post('/delete_answer', 'TestController@deleteAnswer');
