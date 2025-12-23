<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ensure posts directory exists
        if (!file_exists(storage_path('app/public/posts'))) {
            mkdir(storage_path('app/public/posts'), 0755, true);
        }

        // Generate a random image using a placeholder service (picsum)
        $imagePath = null;
        try {
            // Using a unique ID to avoid caching if we were using the same URL repeatedly rapidly
            // picsum.photos/seed/{seed}/640/480
            $seed = Str::random(5);
            $imageUrl = "https://picsum.photos/seed/{$seed}/640/480";
            
            // Set a timeout context
            $ctx = stream_context_create(['http' => ['timeout' => 5]]);
            $contents = @file_get_contents($imageUrl, false, $ctx);
            
            if ($contents) {
                $filename = 'posts/' . Str::random(10) . '.jpg';
                file_put_contents(storage_path('app/public/' . $filename), $contents);
                $imagePath = $filename;
            }
        } catch (\Exception $e) {
            // Fail silently if image cannot be downloaded
        }

        return [
            'title' => fake()->sentence(),
            'content' => fake()->paragraphs(3, true),
            'image_path' => $imagePath,
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'category_id' => Category::inRandomOrder()->first()?->id,
            'created_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'updated_at' => now(),
        ];
    }
}
