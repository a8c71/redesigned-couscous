<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TagTest extends TestCase
{
	use RefreshDatabase;

  public function testGetTags()
  {
		factory(\App\Tag::class, 100)->create();

		$response = $this->json('GET', "/api/tags");
		$response->assertStatus(200)
			->assertJsonStructure(['tags' => [['id', 'name']]]);
  }
  
  public function testGetTagsWithQuestions()
  {
		factory(\App\Question::class, 5)->create()->each(function($q) {
			$q->tags()->attach(factory(\App\Tag::class, 3)->create());
		});

		$response = $this->json('GET', "/api/tags/questions");
    $response->assertStatus(200)
    	->assertJson(['tags' => array()])
    	->assertJsonStructure(['tags' => [['name', 'questions' => [['id', 'title', 'body']]]]]);
  }
}
