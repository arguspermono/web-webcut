<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaEdit extends Model
{
    use HasUuids;

    protected $fillable = [
        'media_id',
        'edit_params',
        'output_path',
        'status',
    ];

    protected $casts = [
        'edit_params' => 'array',
    ];

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }
}
