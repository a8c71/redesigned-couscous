<?php

use Faker\Generator as Faker;

/** 
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */
$factory->define(App\Question::class, function (Faker $faker) {
	$user = factory(App\User::class)->create();
  return [
  	'user_id' => function () use ($user){
      return $user->id;
    },
  	'title' => $faker->sentence(10),
  	'body' => $faker->text(200)
  ];
});
