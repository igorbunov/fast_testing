<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('main');
});

Route::get('/edit/{editSlug}', 'TestController@showEditTest');
Route::post('/create_new_test', 'TestController@startNewTest');
Route::post('/get_answer_form', 'TestController@getAnswerForm');

Route::post('/get_question_form', 'TestController@getQuestionForm');
Route::post('/delete_question', 'TestController@deleteQuestion');
Route::post('/delete_answer', 'TestController@deleteAnswer');
Route::post('/save_question', 'TestController@saveQuestion');
