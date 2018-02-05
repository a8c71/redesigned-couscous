<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SolveAnswerRequest;
use App\Question;
use App\Comment;

class AnswersController extends Controller
{
	public function index(Question $question)
	{
		return response()->json([
			'answers' => $question->answers()->with([
				'comments' => function($q){ 
					$q->with('user:id,nickname')->select('id','body','comment_id','user_id','created_at'); 
				}, 
				'user:id,nickname'])->get()
		]);
	}

	public function store(Question $question)
	{
		$validatedData = request()->validate([
      'body' => 'required'
    ]);

    $validatedData['user_id'] = request()->user()->id;


    $answer = $question->answers()->create($validatedData);
    return response()->json(['answer' => $answer], 201);
	}

	public function solution(Comment $answer, SolveAnswerRequest $request)
	{
		$answer->question()->first()->answers()->update(['solution' => 'NO']);
		$answer->update(['solution' => 'YES']);
		return response()->json(['answer' => $answer]);
	}
}
