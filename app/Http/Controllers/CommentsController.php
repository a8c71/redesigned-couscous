<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Comment;

class CommentsController extends Controller
{
	public function store(Comment $answer)
	{
		$validatedData = request()->validate([
			'body' => 'required'
		]);

    $validatedData['user_id'] = request()->user()->id;

		$comment = $answer->comments()->create($validatedData);

		return response()->json(['comments' => $answer->comments()->with('user:id,nickname')->get()], 201);
	}
}
