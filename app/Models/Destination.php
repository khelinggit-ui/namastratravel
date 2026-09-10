<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Destination extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug', 'name', 'country', 'type', 'description', 'image', 'sort',
    ];

    public function gallery(): HasMany
    {
        return $this->hasMany(DestinationGallery::class)->orderBy('sort');
    }

    public function tours(): BelongsToMany
    {
        return $this->belongsToMany(Tour::class)->orderBy('sort');
    }
}