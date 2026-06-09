<?php

namespace App\Jobs;

use App\Models\Media;
use App\Models\MediaEdit;
use App\Services\FFmpegService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Storage;

class ProcessEditJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;

    protected MediaEdit $mediaEdit;
    protected Media     $media;

    public function __construct(MediaEdit $mediaEdit, Media $media)
    {
        $this->mediaEdit = $mediaEdit;
        $this->media     = $media;
    }

    public function handle(FFmpegService $ffmpegService): void
    {
        try {
            $format = $this->mediaEdit->edit_params['format'] ?? 'mp4';
            $outputPath = 'media/edits/' . $this->mediaEdit->id . '.' . $format;

            $success = $ffmpegService->applyEdits(
                $this->media->storage_path,
                $outputPath,
                $this->mediaEdit->edit_params
            );

            if ($success) {
                $this->mediaEdit->update([
                    'output_path' => $outputPath,
                    'status'      => 'ready',
                ]);

                // Extract new thumbnails for the edited video
                $thumbnailPath = 'media/thumbnails/' . $this->media->id . '.jpg';
                $sequenceDir   = 'media/thumbnails/' . $this->media->id;

                // Delete old sequence directory to avoid leftover frame images
                Storage::disk('public')->deleteDirectory($sequenceDir);

                // Extract single poster thumbnail and sequence thumbnails
                $ffmpegService->extractThumbnail($outputPath, $thumbnailPath);
                $ffmpegService->extractThumbnailSequence($outputPath, $sequenceDir, 1.0);

                // Get new duration and size from the exported file
                $newDuration = $ffmpegService->getDuration($outputPath);
                $newSize     = Storage::disk('public')->size($outputPath);

                // Replace the media's active file with the exported edit
                // so the watch/stream page always serves the latest exported version
                $this->media->update([
                    'storage_path'   => $outputPath,
                    'thumbnail_path' => $thumbnailPath,
                    'duration'       => $newDuration ?? $this->media->duration,
                    'size_bytes'     => $newSize ?: $this->media->size_bytes,
                ]);
            } else {
                throw new \Exception("FFmpeg edit failed for MediaEdit ID: {$this->mediaEdit->id}");
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->mediaEdit->update(['status' => 'failed']);
            $this->fail($e);
        }
    }
}
