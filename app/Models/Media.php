<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Media extends Model
{
    use HasUuids;

    protected $fillable = [
        'id',
        'original_filename',
        'storage_path',
        'thumbnail_path',
        'status',
        'duration',
        'resolution',
        'format',
        'mime_type',
        'size_bytes'
    ];

    public function edits()
    {
        return $this->hasMany(MediaEdit::class);
    }
}
