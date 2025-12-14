<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        // Generate 10 posts using the factory (which handles image generation)
        Post::factory(10)->create();
    }
}
