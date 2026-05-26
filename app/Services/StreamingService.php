<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StreamingService
{
    /**
     * Stream a media file using HTTP Range requests.
     */
    public function stream(Media $media, Request $request): StreamedResponse
    {
        $path = Storage::disk('public')->path($media->storage_path);

        if (!file_exists($path)) {
            abort(404, 'File not found');
        }

        $size = filesize($path);
        $time = gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT';

        $fm = @fopen($path, 'rb');
        if (!$fm) {
            abort(500, 'Could not open file');
        }

        $start = 0;
        $end = $size - 1;

        $range = $request->header('Range');
        $status = 200;

        if ($range) {
            if (preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $range, $matches)) {
                $start = intval($matches[1]);
                if (!empty($matches[2])) {
                    $end = intval($matches[2]);
                }
            }
            $status = 206; // Partial Content
        }

        $length = $end - $start + 1;
        fseek($fm, $start);

        $headers = [
            'Content-Type' => 'video/mp4', // Assuming standardized to mp4 in Phase 4
            'Cache-Control' => 'max-age=2592000, public',
            'Expires' => gmdate('D, d M Y H:i:s', time() + 2592000) . ' GMT',
            'Last-Modified' => $time,
            'Accept-Ranges' => 'bytes',
            'Content-Length' => $length,
            'Content-Range' => "bytes {$start}-{$end}/{$size}",
        ];

        return new StreamedResponse(function () use ($fm, $length) {
            $bufferSize = 8192; // 8KB chunks
            $bytesSent = 0;

            while (!feof($fm) && $bytesSent < $length && (connection_status() == 0)) {
                $bytesToRead = min($bufferSize, $length - $bytesSent);
                echo fread($fm, $bytesToRead);
                flush();
                $bytesSent += $bytesToRead;
            }

            fclose($fm);
        }, $status, $headers);
    }
}
