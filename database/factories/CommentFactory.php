<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'post_id' => Post::factory(),
            'author_name' => $this->faker->name(),
            'author_email' => $this->faker->safeEmail(),
            'content' => $this->faker->paragraphs(2, true),
            'is_approved' => false,
            'likes_count' => 0,
            'dislikes_count' => 0,
        ];
    }
}
