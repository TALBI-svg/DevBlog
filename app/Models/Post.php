<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content', 'image_path', 'user_id', 'category_id'];

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'likes')
            ->withTimestamps()
            ->orderByPivot('created_at', 'desc');
    }

    public function isLikedBy(User $user): bool
    {
        return $this->likes->contains($user);
    }
}
