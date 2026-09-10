<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug', 'title', 'category', 'author', 'published_at', 'cover', 'excerpt', 'body', 'status',
    ];

    protected $casts = [
        'published_at' => 'date',
    ];

    public function getBodyParagraphsAttribute(): array
    {
        if (is_string($this->body) && str_contains($this->body, "\n\n")) {
            return preg_split('/\n\s*\n/', trim($this->body)) ?: [$this->body];
        }
        return is_array($this->body) ? $this->body : [$this->body];
    }
}