<?php

use Illuminate\Http\Request;

Route::group(['middleware' => ['auth:api']], function() {
	Route::post('question', 'QuestionsController@store');
	Route::post('question/{question}/answer', 'AnswersController@store');
	Route::delete('question/{id}', 'QuestionsController@destroy');
	Route::post('answer/{answer}/comment', 'CommentsController@store');
	Route::post('answer/{answer}/vote', 'VotesController@store');
	Route::patch('answer/{answer}/correct', 'AnswersController@solution');
});
Route::get('questions', 'QuestionsController@index');
Route::get('question/{question}', 'QuestionsController@show');
Route::get('question/{question}/answers', 'AnswersController@index');
Route::get('tags', 'TagsController@index');
Route::get('tags/questions', 'TagsController@indexWithQuestions');
