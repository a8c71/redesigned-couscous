<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tag;

class TagsController extends Controller
{
	public function index()
	{
		return response()->json(['tags' => Tag::all()]);
	}

  public function indexWithQuestions()
  {
  	return response()->json(['tags' => Tag::with(['questions' => function ($q) {
  		$q->with('user:id,nickname');
  	}])->get()]);
  }
}
