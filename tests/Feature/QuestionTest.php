<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;

class QuestionTest extends TestCase
{
	use RefreshDatabase;

  public function testFailsWhenNoUserLoggedIn()
  {
  	$response = $this->json('POST', '/api/question');

    $response->assertStatus(401);
  }

  public function testFailsWhenNoRequiredInputsAreSupplied()
  {
  	$user = factory(\App\User::class)->create();
  	Passport::actingAs($user);

  	$response = $this->json('POST', '/api/question');

  	$response->assertStatus(422);
  }

  public function testCreateQuestion()
  {
  	$user = factory(\App\User::class)->create();
  	Passport::actingAs($user);

  	$response = $this->json('POST', '/api/question', [
  		'title' => 'Test title',
  		'body' => 'Test body'
  	]);

  	$response->assertSuccessful();
  }

  public function testGetQuestions()
  {
  	factory(\App\Question::class)->create();

  	$response = $this->json('GET', '/api/questions');

  	$response->assertJson(['questions' => []]);
  	$response->assertJsonCount(1, 'questions');
	}

	public function testDeleteQuestion()
	{
		$user = factory(\App\User::class)->create();
		$question = $user->questions()->save(factory(\App\Question::class)->make());
  	Passport::actingAs($user);

		$response = $this->json('DELETE', "/api/question/$question->id");

		$response->assertSuccessful();
		$this->assertSoftDeleted('questions', ['id' => $question->id]);
	}

}
