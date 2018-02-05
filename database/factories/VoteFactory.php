<?php

use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Vote::class, function (Faker $faker) {
	$user = factory(App\User::class)->create();
	$comment = factory(App\Comment::class)->create();
  return [
  	'user_id' => function () use($user){
      return $user->id;
    },
  	'comment_id' => function () use($comment){
      return $comment->id;
    },
  	'value' => $faker->randomElement([1, -1]),
  ];
});

$factory->state(App\Vote::class, 'upvote', [
    'value' => 1,
]);

$factory->state(App\Vote::class, 'downvote', [
    'value' => -1,
]);
