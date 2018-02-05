<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;

class AnswerTest extends TestCase
{
	use RefreshDatabase;

	public function testGetQuestionAnswers()
	{
		$user = factory(\App\User::class)->create();
		$question = factory(\App\Question::class)->create(['user_id' => $user->id]);
		$answers = factory(\App\Comment::class, 5)->create([
			'question_id' => $question->id,
			'user_id' => $user->id
		]);
		$question->answers->add($answers);
		

  	$response = $this->json('GET', "/api/question/$question->id/answers");

  	$response->assertStatus(200)
			->assertJson(['answers' => array()])
			->assertJsonCount(5, 'answers');
	}

	public function testGetQuestionAnswersWithCommentsAndCommentData()
	{
		$user = factory(\App\User::class)->create();
		$question = factory(\App\Question::class)->create(['user_id' => $user->id]);
		$answers = factory(\App\Comment::class, 5)->create([
			'question_id' => $question->id
		])->each(function ($answer){
			$answer->comments
				->add(factory(\App\Comment::class, 6)
					->create(['comment_id' => $answer->id])
					->each(function ($comment){
						$comment->votes
							->add(factory(\App\Vote::class, 6)->create(['comment_id' => $comment->id]));
					})
				);
			$answer->votes
				->add(factory(\App\Vote::class, 15)->create(['comment_id' => $answer->id]));
		});
		$question->answers->add($answers);

		$response = $this->json('GET', "/api/question/$question->id/answers");

		$response->assertStatus(200)
			->assertJsonStructure([
				'answers' => [[
					'id', 
					'score', 
					'body', 
					'solution', 
					'created_at', 
					'comments' => [[
						'id', 
						'body', 
						'created_at',
						'votes' => [['value']]
					]],
					'user' => ['id','nickname'],
					'votes' => [['value']]
				]]
			]);
	}

	public function testCreateAnswerFailsWhenNoUser()
	{
		$user = factory(\App\User::class)->create();
		$question = factory(\App\Question::class)->create(['user_id' => $user->id]);

    $response = $this->json('POST', "/api/question/$question->id/answer", [
    	'body' => 'example'
    ]);

    $response->assertStatus(401);
	}

  public function testCreateAnswer()
  {
		$user = factory(\App\User::class)->create();
		$question = factory(\App\Question::class)->create(['user_id' => $user->id]);
		Passport::actingAs($user);

    $response = $this->json('POST', "/api/question/$question->id/answer", [
    	'body' => 'example'
    ]);

    $response->assertStatus(201)
			->assertJson(['answer' => [
				'body' => 'example',
				'question_id' => $question->id
			]]);
  }

  public function testUsersCanVoteOnAnswers()
  {
  	$user = factory(\App\User::class)->create();
		$question = factory(\App\Question::class)->create(['user_id' => $user->id]);
		$answers = factory(\App\Comment::class, 5)->create([
			'question_id' => $question->id,
			'user_id' => $user->id
		]);
		$question->answers->add($answers);

		Passport::actingAs($user);

		$response = $this->json('POST', "/api/answer/1/vote", [
    	'value' => 1
    ]);
    $response->assertStatus(201);
  }

  public function testVotesOnAnswerUpdatesScore()
  {
		$question = factory(\App\Question::class)->create();
  	$answer = factory(\App\Comment::class)->create(['question_id' => $question->id]);
  	$upVotes = factory(\App\Vote::class, 10)
  		->states('upvote')
  		->create(['comment_id' => $answer->id]);
  	$downVotes = factory(\App\Vote::class, 2)
  		->states('downvote')
  		->create(['comment_id' => $answer->id]);

		$response = $this->json('GET', "/api/question/$question->id/answers");
    $response->assertStatus(200)
    	->assertJsonFragment([
    		'score' => 8
    	]);
  }

  public function testUserVoteOnAnswerCountsOnce()
  {
  	$user = factory(\App\User::class)->create();
		$question = factory(\App\Question::class)->create();
		$answer = factory(\App\Comment::class)->create([
			'question_id' => $question->id
		]);
		$answer->votes->add(factory(\App\Vote::class, 5)->states('upvote')->create([
			'comment_id' => $question->id
		]));

		Passport::actingAs($user);

		$response = $this->json('POST', "/api/answer/$answer->id/vote", [
			'value' => 1
		]);

		$response->assertStatus(201);
		$answer->refresh();

		$this->assertEquals(6, $answer->score);

		$otherResponse = $this->json('POST', "/api/answer/$answer->id/vote", [
			'value' => 1
		]);

		$otherResponse->assertStatus(201);
		$answer->refresh();

		$this->assertEquals(5, $answer->score);
  }

  public function testQuestionOwnerCanChooseCorrectAnswer()
  {
  	$user = factory(\App\User::class)->create();
		$question = factory(\App\Question::class)->create(['user_id' => $user->id]);
		$answer = factory(\App\Comment::class)->create(['question_id' => $question->id]);

		Passport::actingAs($user);

		$response = $this->json('PATCH', "/api/answer/$answer->id/correct");
		$response->assertStatus(200);

		$answer->refresh();
		$this->assertEquals('YES', $answer->solution);
  }

  public function testQuestionOwnerCanChangeCorrectAnswer()
  {
  	$user = factory(\App\User::class)->create();
		$question = factory(\App\Question::class)->create(['user_id' => $user->id]);
		$correctAnswer = factory(\App\Comment::class)
			->states('correct')
			->create(['question_id' => $question->id]);
		$newAnswer = factory(\App\Comment::class)->create(['question_id' => $question->id]);

		Passport::actingAs($user);

		$response = $this->json('PATCH', "/api/answer/$newAnswer->id/correct");
		$response->assertStatus(200);

		$correctAnswer->refresh();
		$newAnswer->refresh();
		$this->assertEquals('NO', $correctAnswer->solution);
		$this->assertEquals('YES', $newAnswer->solution);
  } 

  public function testOnlyQuestionOwnerCanChooseCorrectAnswer()
  {
  	$owner = factory(\App\User::class)->create();
		$question = factory(\App\Question::class)->create(['user_id' => $owner->id]);
		$answer = factory(\App\Comment::class)->create(['question_id' => $question->id]);

  	$user = factory(\App\User::class)->create();
		Passport::actingAs($user);

		$response = $this->json('PATCH', "/api/answer/$answer->id/correct");
		$response->assertStatus(403);

		$answer->refresh();
		$this->assertEquals('NO', $answer->solution);
  }
}
