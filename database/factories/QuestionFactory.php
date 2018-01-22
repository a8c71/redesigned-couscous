<?php

use Faker\Generator as Faker;

/** 
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */
$factory->define(App\Question::class, function (Faker $faker) {
  return [
  	'title' => $faker->sentence(10),
  	'body' => $faker->text(200),
  	'user_id' => function () {
      return factory(App\User::class)->create()->id;
    }
  ];
});
