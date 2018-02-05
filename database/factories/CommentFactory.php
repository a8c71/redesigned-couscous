<?php

use Faker\Generator as Faker;

/** 
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */
$factory->define(App\Comment::class, function (Faker $faker) {
  return [
  	'user_id' => function () {
      return factory(App\User::class)->create()->id;
    },
  	'body' => $faker->paragraphs(3, true),
  	'solution' => 'NO',
  	'question_id' => null,
  	'comment_id' => null,
  ];
});

$factory->state(App\Comment::class, 'correct', [
	'solution' => 'YES'
]);