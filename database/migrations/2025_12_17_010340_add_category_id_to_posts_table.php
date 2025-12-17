<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('content')->constrained()->onDelete('set null');
        });

        // Assign a random category to existing posts
        $categoryIds = DB::table('categories')->pluck('id')->toArray();
        if (!empty($categoryIds)) {
            $posts = DB::table('posts')->get();
            foreach ($posts as $post) {
                DB::table('posts')
                    ->where('id', $post->id)
                    ->update(['category_id' => $categoryIds[array_rand($categoryIds)]]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
