<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;

class QuestionTest extends TestCase
{
	use RefreshDatabase;

  public function testGetQuestions()
  {
  	$question = factory(\App\Question::class)->create();
  	$tags = factory(\App\Tag::class)->create();
  	$question->tags()->attach($tags);
  	
  	$response = $this->json('GET', '/api/questions');

  	$response->assertJson(['questions' => array()])
  		->assertJsonStructure([
  			'questions' => [[
  				'body', 
  				'title', 
  				'answers_count',
  				'user' => ['id', 'nickname'],
  				'tags' => [['name']]
  			]]
  		]);
	}
/*
	public function testGetQuestionIncreasesViewsOnce()
	{
		$user = factory(\App\User::class)->create();
  	Passport::actingAs($user);

		$question = factory(\App\Question::class)->create();
		$this->assertEquals(0, $question->view_count);

		$response = $this->json('GET', "/api/question/$question->id");
		$response->assertStatus(200);

		$question->refresh();

		$this->assertEquals(1, $question->view_count);

		$anotherResponse = $this->json('GET', "/api/question/$question->id");
		$anotherResponse->assertStatus(200);

		$question->refresh();

		$this->assertEquals(1, $question->view_count);
	}
*/
  public function testCreateQuestion()
  {
  	$user = factory(\App\User::class)->create();
  	Passport::actingAs($user);

  	$response = $this->json('POST', '/api/question', [
  		'title' => 'Test title',
  		'body' => 'Test body'
  	]);

  	$response->assertStatus(201);
  }

  public function testCreateQuestionWithTags()
  {
  	$user = factory(\App\User::class)->create();
  	Passport::actingAs($user);

  	$response = $this->json('POST', '/api/question', [
  		'title' => 'Test title',
  		'body' => 'Test body',
  		'tags' => [
  			'general',
  			'life'
  		]
  	]);

  	$response->assertStatus(201);
  	$this->assertEquals(2, \App\Question::latest()->first()->tags()->count());
  }

  public function testGetSingleQuestion()
  {
  	$user = factory(\App\User::class)->create();
		$question = factory(\App\Question::class)->create(['user_id' => $user->id]);
		$answers = factory(\App\Comment::class, 5)->create([
			'question_id' => $question->id,
			'user_id' => $user->id
		]);
		$question->answers->add($answers);
		$answers[0]->comments->add(factory(\App\Comment::class, 3)->create([
			'comment_id' => $answers[0]->id
		]));
		$tags = factory(\App\Tag::class, 3)->create();
		$question->tags()->attach($tags);
		
  	Passport::actingAs($user);

		$response = $this->json('GET', "/api/question/$question->id");
    $response->assertStatus(200)
    	->assertJson(['question' => array()])
    	->assertJsonCount(3, 'question.tags')
    	->assertJsonCount(5, 'question.answers')
    	->assertJsonStructure([
    		'question' => [
    			'id',
    			'body',
    		  'answers' => [[
  					'id',
  					'body',
  					'score',
  					'comments' => [[
  						'id',
  						'body',
  						'score',
  						'user' => [
  							'id',
  							'nickname'
  						]
  					]],
  					'user' => [
							'id',
							'nickname'
						]
  				]],
  				'tags' => [['name']],
  				'user' => [
  					'id',
  					'nickname'
  				]
    		]
    	]);
  }

	public function testDeleteQuestion()
	{
		$user = factory(\App\User::class)->create();
		$question = $user->questions()->save(factory(\App\Question::class)->make());
  	Passport::actingAs($user);

		$response = $this->json('DELETE', "/api/question/$question->id");

		$response->assertStatus(200);
		$this->assertSoftDeleted('questions', ['id' => $question->id]);
	}

  public function testCreateQuestionFailsWhenNoUserLoggedIn()
  {
  	$response = $this->json('POST', '/api/question');

    $response->assertStatus(401);
  }

  public function testCreateQuestionFailsWhenNoRequiredInputsAreSupplied()
  {
  	$user = factory(\App\User::class)->create();
  	Passport::actingAs($user);

  	$response = $this->json('POST', '/api/question');

  	$response->assertStatus(422);
  }



}
