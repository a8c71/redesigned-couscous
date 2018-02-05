<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Comment;

class VotesController extends Controller
{
  public function store(Comment $answer)
  {
  	$validatedData = request()->validate([
      'value' => 'required',
    ]);
    $validatedData['user_id'] = request()->user()->id;

    $answer->load(['votes:comment_id,user_id,value', 'user:id,nickname'])->get();
    $existingVote = $answer->votes
    	->where('user_id', $validatedData['user_id'])
    	->first();

    if ($existingVote) {
    	if ($existingVote->value != $validatedData['value']) $answer->votes()->create($validatedData);
    	$existingVote->delete();
    } else {
    	$answer->votes()->create($validatedData);
    }

    return response()->json(['answer' => $answer], 201);
  }
}
