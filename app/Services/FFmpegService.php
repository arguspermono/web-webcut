<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class FFmpegService
{
    /**
     * Transcode the video to standardized MP4 (H.264, YUV420p, faststart).
     */
    public function transcode(string $inputPath, string $outputPath): bool
    {
        // Ensure directory exists
        Storage::disk('public')->makeDirectory(dirname($outputPath));

        $fullInputPath = Storage::disk('public')->path($inputPath);
        $fullOutputPath = Storage::disk('public')->path($outputPath);

        $command = [
            'ffmpeg', '-y', '-i', $fullInputPath,
            '-c:v', 'libx264',
            '-preset', 'fast',
            '-crf', '23',
            '-pix_fmt', 'yuv420p',
            '-movflags', '+faststart',
            $fullOutputPath
        ];

        return $this->runProcess($command);
    }

    /**
     * Extract a thumbnail from the video at a specific time.
     */
    public function extractThumbnail(string $inputPath, string $outputPath, string $time = '00:00:01'): bool
    {
        Storage::disk('public')->makeDirectory(dirname($outputPath));

        $fullInputPath = Storage::disk('public')->path($inputPath);
        $fullOutputPath = Storage::disk('public')->path($outputPath);

        $command = [
            'ffmpeg', '-y', '-ss', $time, '-i', $fullInputPath,
            '-vframes', '1',
            '-q:v', '2',
            $fullOutputPath
        ];

        return $this->runProcess($command);
    }

    /**
     * Helper to run Symfony Process.
     */
    protected function runProcess(array $command): bool
    {
        $process = new Process($command);
        $process->setTimeout(3600); // 1 hour max

        try {
            $process->mustRun();
            return true;
        } catch (ProcessFailedException $e) {
            Log::error('FFMPEG Process Failed: ' . $e->getMessage());
            return false;
        }
    }
}
