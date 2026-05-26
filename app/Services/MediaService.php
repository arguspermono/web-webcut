<?php

namespace App\Services;

use App\Models\Media;
use App\Jobs\InitialTranscodeJob;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MediaService
{
    /**
     * Process an uploaded video file.
     */
    public function processUpload(UploadedFile $file): Media
    {
        // 1. Generate unique UUID and path
        $uuid = Str::uuid()->toString();
        $extension = $file->getClientOriginalExtension();
        $filename = "{$uuid}.{$extension}";
        
        // 2. Store original file
        $path = $file->storeAs('media/original', $filename, 'public');
        
        // 3. Create DB record
        $media = Media::create([
            'id' => $uuid,
            'original_filename' => $file->getClientOriginalName(),
            'storage_path' => $path,
            'status' => 'uploading',
            'mime_type' => $file->getMimeType(),
            'size_bytes' => $file->getSize(),
        ]);
        
        // 4. Update status and dispatch job
        $media->update(['status' => 'processing']);
        InitialTranscodeJob::dispatch($media);
        
        return $media;
    }
}
