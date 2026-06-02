<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class FFmpegService
{
    /**
     * Get the full path to the ffmpeg binary.
     * Reads FFMPEG_BINARY from .env, falls back to 'ffmpeg' (relies on PATH).
     */
    protected function ffmpegBin(): string
    {
        return env('FFMPEG_BINARY', 'ffmpeg');
    }

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
            $this->ffmpegBin(), '-y', '-i', $fullInputPath,
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
            $this->ffmpegBin(), '-y', '-ss', $time, '-i', $fullInputPath,
            '-vframes', '1',
            '-q:v', '2',
            $fullOutputPath
        ];

        return $this->runProcess($command);
    }

    /**
     * Apply complex edits (trim, crop, speed, mute) in a single optimized FFmpeg command.
     *
     * @param string $inputPath  Storage-relative path of the source transcoded file.
     * @param string $outputPath Storage-relative path where the edit output will be saved.
     * @param array  $params     Edit parameters validated by StoreMediaEditRequest.
     */
    public function applyEdits(string $inputPath, string $outputPath, array $params): bool
    {
        Storage::disk('public')->makeDirectory(dirname($outputPath));

        $fullInput  = Storage::disk('public')->path($inputPath);
        $fullOutput = Storage::disk('public')->path($outputPath);

        // ── Build command ─────────────────────────────────────────────────────
        $cmd = [$this->ffmpegBin(), '-y'];

        // 1. Trim: input-seeking (fast, stream-copy friendly)
        $startTime = isset($params['start_time']) ? (float) $params['start_time'] : 0;
        $endTime   = isset($params['end_time'])   ? (float) $params['end_time']   : null;
        $cmd[] = '-ss';
        $cmd[] = number_format($startTime, 6, '.', '');
        if ($endTime !== null) {
            $cmd[] = '-to';
            $cmd[] = number_format($endTime, 6, '.', '');
        }

        $cmd[] = '-i';
        $cmd[] = $fullInput;

        // ── Video filter chain ─────────────────────────────────────────────────
        $vFilters = [];

        // 2. Crop
        if (!empty($params['crop_w']) && !empty($params['crop_h'])) {
            $w = (int) $params['crop_w'];
            $h = (int) $params['crop_h'];
            $x = (int) ($params['crop_x'] ?? 0);
            $y = (int) ($params['crop_y'] ?? 0);
            $vFilters[] = "crop={$w}:{$h}:{$x}:{$y}";
        }

        // 3. Speed (setpts for video; atempo for audio — chained)
        $speed = isset($params['speed']) ? (float) $params['speed'] : 1.0;
        if ($speed !== 1.0) {
            $pts = round(1 / $speed, 6);
            $vFilters[] = "setpts={$pts}*PTS";
        }

        if (!empty($vFilters)) {
            $cmd[] = '-vf';
            $cmd[] = implode(',', $vFilters);
        }

        // ── Audio ──────────────────────────────────────────────────────────────
        if (!empty($params['mute'])) {
            $cmd[] = '-an'; // Strip audio entirely
        } elseif ($speed !== 1.0) {
            // Adjust audio speed using atempo (must be between 0.5–2.0; chain for >2.0)
            $aFilters = $this->buildAtempoFilters($speed);
            $cmd[] = '-af';
            $cmd[] = implode(',', $aFilters);
        }

        // ── Output encoding ────────────────────────────────────────────────────
        $cmd = array_merge($cmd, [
            '-c:v', 'libx264',
            '-preset', 'fast',
            '-crf', '23',
            '-pix_fmt', 'yuv420p',
            '-movflags', '+faststart',
            $fullOutput
        ]);

        return $this->runProcess($cmd);
    }

    /**
     * Build atempo audio filter chain. atempo only accepts 0.5–2.0 per instance,
     * so we chain multiple for values outside that range.
     */
    protected function buildAtempoFilters(float $speed): array
    {
        $filters = [];
        while ($speed > 2.0) {
            $filters[] = 'atempo=2.0';
            $speed /= 2.0;
        }
        while ($speed < 0.5) {
            $filters[] = 'atempo=0.5';
            $speed /= 0.5;
        }
        $filters[] = 'atempo=' . round($speed, 6);
        return $filters;
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
