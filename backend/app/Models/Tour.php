<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tour extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug', 'title', 'category', 'duration', 'price_start', 'location',
        'tagline', 'description', 'highlights', 'image', 'tag', 'status', 'sort',
        'itinerary', 'departure_schedules',
    ];

    protected $casts = [
        'price_start' => 'integer',
        'highlights' => 'array',
        'itinerary' => 'array',
        'departure_schedules' => 'array',
    ];

    public function gallery(): HasMany
    {
        return $this->hasMany(TourGallery::class)->orderBy('sort');
    }

    public function destinations(): BelongsToMany
    {
        return $this->belongsToMany(Destination::class);
    }

    public function getIsPublishedAttribute(): bool
    {
        return $this->status === 'published';
    }
}
