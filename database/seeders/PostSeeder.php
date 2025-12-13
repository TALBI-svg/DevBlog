<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('posts')->insert([
            [
                'title' => 'First Post',
                'description' => 'This is the first post description.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Second Post',
                'description' => 'This is the second post description.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Third Post',
                'description' => 'This is the third post description.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
