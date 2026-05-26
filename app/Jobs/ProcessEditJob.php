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
            $outputPath = 'media/edits/' . $this->mediaEdit->id . '.mp4';

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
