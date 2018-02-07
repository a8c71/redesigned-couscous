<?php

use Illuminate\Database\Seeder;

class QuestionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = factory(\App\User::class)->create();
				$questions = factory(\App\Question::class, 10)
					->create(['user_id' => $user->id])
					->each(function ($question){
						$answers = factory(\App\Comment::class, 5)->create(['question_id' => $question->id]);
						$question->answers->add($answers);
						$correctAnswer = factory(\App\Comment::class)->states('correct')->create(['question_id' => $question->id]);
						$question->answers->add($correctAnswer);
						$answers[0]->comments->add(
							factory(\App\Comment::class, 3)->create([
								'comment_id' => $answers[0]->id
							])
							->each(function ($comment) {
								factory(\App\Vote::class, 6)->create(['comment_id' => $comment->id]);
							}
						));
						$tags = factory(\App\Tag::class, 3)->create();
						$question->tags()->attach($tags);
						factory(\App\Vote::class, 10)->create(['comment_id' => $answers[0]->id]);
					});
    }
}
