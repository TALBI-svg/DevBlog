<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Post;

class AdditionalUserLikesSeeder extends Seeder
{
    public function run(): void
    {
        // Create 15 new users
        $users = User::factory(15)->create();

        // Get all posts
        $posts = Post::all();

        if ($posts->isEmpty()) {
            $this->command->info('No posts found. Skipping likes generation.');
            return;
        }

        // Make users like random posts
        foreach ($users as $user) {
            // Each user likes 5 to all posts (randomly)
            $postsToLike = $posts->random(min($posts->count(), rand(5, $posts->count())));
            
            foreach ($postsToLike as $post) {
                $post->likes()->attach($user->id);
            }
        }
        
        $this->command->info('Created 15 users and assigned random likes.');
    }
}
