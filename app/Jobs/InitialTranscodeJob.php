<?php

namespace App\Jobs;

use App\Models\Media;
use App\Services\FFmpegService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class InitialTranscodeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 hour timeout for transcoding

    protected Media $media;

    /**
     * Create a new job instance.
     */
    public function __construct(Media $media)
    {
        $this->media = $media;
    }

    /**
     * Execute the job.
     */
    public function handle(FFmpegService $ffmpegService): void
    {
        try {
            $inputPath = $this->media->storage_path;
            
            // Define output paths
            $transcodedPath = 'media/transcoded/' . $this->media->id . '.mp4';
            $thumbnailPath = 'media/thumbnails/' . $this->media->id . '.jpg';

            // 1. Transcode
            $transcodeSuccess = $ffmpegService->transcode($inputPath, $transcodedPath);
            
            // 2. Extract Thumbnail
            $thumbSuccess = $ffmpegService->extractThumbnail($transcodedPath, $thumbnailPath);

            if ($transcodeSuccess && $thumbSuccess) {
                // Update DB on success
                $this->media->update([
                    'storage_path' => $transcodedPath, // Point to transcoded file now
                    'thumbnail_path' => $thumbnailPath,
                    'status' => 'ready'
                ]);
            } else {
                throw new \Exception("FFmpeg processing failed for media ID: {$this->media->id}");
            }

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->media->update(['status' => 'failed']);
            
            // Optional: you can fail the job explicitly
            $this->fail($e);
        }
    }
}
