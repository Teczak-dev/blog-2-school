<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CommentVote>
 */
class CommentVoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'comment_id' => \App\Models\Comment::factory(),
            'user_id' => \App\Models\User::factory(),
            'vote_type' => $this->faker->randomElement(['like', 'dislike']),
        ];
    }
}
