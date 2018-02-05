<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;

class CommentTest extends TestCase
{
	use RefreshDatabase;

	public function testPostComment()
	{
		$user = factory(\App\User::class)->create();

		$question = factory(\App\Question::class)->create();
		$answer = factory(\App\Comment::class)->create(['question_id' => $question->id]);

  	Passport::actingAs($user);

		$response = $this->json('POST', "/api/answer/$answer->id/comment", [
			'body' => 'example'
		]);

		$response->assertStatus(201)
			->assertJson(['comments' => array()])
			->assertJsonStructure(['comments' => [['user' => ['id', 'nickname']]]]);

	}

  public function testAnswerComesWithComments()
  {
  	$user = factory(\App\User::class)->create();
		$question = factory(\App\Question::class)->create(['user_id' => $user->id]);
		$answers = factory(\App\Comment::class, 5)->create([
			'question_id' => $question->id,
			'user_id' => $user->id
		])->each(function($a) {
			$a->comments->add(factory(\App\Comment::class, 10)->create([
				'comment_id' => $a->id,
				'user_id' => $a->user_id
			]));
		});
		$question->answers->add($answers);

		$response = $this->json('GET', "/api/question/$question->id/answers");

  	$response->assertStatus(200)
			->assertJson(['answers' => array()])
			->assertJsonCount(5, 'answers')
			->assertJsonCount(10, 'answers.0.comments');
  }

}
