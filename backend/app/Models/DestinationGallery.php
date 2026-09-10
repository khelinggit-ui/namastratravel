<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DestinationGallery extends Model
{
    public $timestamps = false;

    protected $table = 'destination_gallery';

    protected $fillable = ['destination_id', 'image', 'sort'];

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }
}