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
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class InitialTranscodeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;

    protected Media $media;

    public function __construct(Media $media)
    {
        $this->media = $media;
    }

    /**
     * Check whether ffmpeg binary is accessible on this system.
     */
    protected function ffmpegAvailable(): bool
    {
        $binary = env('FFMPEG_BINARY', 'ffmpeg');
        $process = new Process([$binary, '-version']);
        try {
            $process->run();
            return $process->isSuccessful();
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Execute the job.
     */
    public function handle(FFmpegService $ffmpegService): void
    {
        try {
            $inputPath    = $this->media->storage_path;
            $transcodedPath = 'media/transcoded/' . $this->media->id . '.mp4';
            $thumbnailPath  = 'media/thumbnails/'  . $this->media->id . '.jpg';

            if (!$this->ffmpegAvailable()) {
                // ── FFmpeg not installed: pass-through the original file ──
                Log::warning("FFmpeg not found. Using original file as pass-through for media [{$this->media->id}].");

                // Copy original to the transcoded path so the stream endpoint works
                $originalAbsolute    = Storage::disk('public')->path($inputPath);
                $transcodedAbsolute  = Storage::disk('public')->path($transcodedPath);

                Storage::disk('public')->makeDirectory('media/transcoded');
                copy($originalAbsolute, $transcodedAbsolute);

                $this->media->update([
                    'storage_path'   => $transcodedPath,
                    'thumbnail_path' => null,
                    'status'         => 'ready',
                ]);

                return;
            }

            // ── FFmpeg available: full transcode + thumbnail ──
            $transcodeSuccess = $ffmpegService->transcode($inputPath, $transcodedPath);
            $thumbSuccess     = $ffmpegService->extractThumbnail($transcodedPath, $thumbnailPath);

            if ($transcodeSuccess && $thumbSuccess) {
                $this->media->update([
                    'storage_path'   => $transcodedPath,
                    'thumbnail_path' => $thumbnailPath,
                    'status'         => 'ready',
                ]);
            } else {
                throw new \Exception("FFmpeg processing failed for media ID: {$this->media->id}");
            }

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->media->update(['status' => 'failed']);
            $this->fail($e);
        }
    }
}
