<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure categories exist
        if (Category::count() == 0) {
            Category::create(['name' => 'Technology', 'slug' => 'technology', 'description' => 'All about tech']);
            Category::create(['name' => 'Design', 'slug' => 'design', 'description' => 'Creative design things']);
            Category::create(['name' => 'Life', 'slug' => 'life', 'description' => 'Lifestyle and daily blogs']);
            Category::create(['name' => 'Coding', 'slug' => 'coding', 'description' => 'Programming tips and tricks']);
        }

        $users = User::all();
        $categories = Category::all();

        foreach ($users as $user) {
            // Create 1-3 posts for each user
            $postsCount = rand(1, 3);
            
            Post::factory($postsCount)->create([
                'user_id' => $user->id,
                'category_id' => $categories->isNotEmpty() ? $categories->random()->id : null,
            ]);
        }
    }
}
