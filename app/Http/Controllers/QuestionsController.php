<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Question;
use App\Tag;

class QuestionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(['questions' => Question::all()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|unique:questions|max:255',
            'body' => 'required'
        ]);

        $question = request()->user()->questions()->create($validatedData);


        $validatedDataTags = $request->validate([
            'tags' => 'array'
        ]);

        if (array_key_exists('tags', $validatedDataTags)) {
            foreach ($validatedDataTags['tags'] as $tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                $question->tags()->attach($tag->id);
            }
        } 

        return response()->json(['question' => $question->refresh()], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Question $question)
    {
        if (($user = request()->user()) && 
            !$user->views()->exists(['question_id' => $question->id])
        ) $user->views()->attach($question->id);
        $question->refresh();
        return response()->json(['question' => $question->load(['answers', 'tags:name'])]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $question = request()->user()->questions()->find($id);
        if ($question) $question->delete();
        return response()->json(['message' => 'Question deleted']);
    }
}
