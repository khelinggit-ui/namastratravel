<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutContent extends Model
{
    protected $table = 'about_content';

    protected $fillable = ['story', 'vision', 'missions', 'stats', 'values'];

    protected $casts = [
        'story' => 'array',
        'missions' => 'array',
        'stats' => 'array',
        'values' => 'array',
    ];

    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1], [
            'story' => [],
            'vision' => '',
            'missions' => [],
            'stats' => [],
            'values' => [],
        ]);
    }
}