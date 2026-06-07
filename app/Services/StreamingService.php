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
        return $this->streamPath($media->storage_path, $request);
    }

    /**
     * Stream a file from a given storage path using HTTP Range requests.
     */
    public function streamPath(string $storagePath, Request $request): StreamedResponse
    {
        $path = Storage::disk('public')->path($storagePath);

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
        $status = 200;

        $headers = [
            'Content-Type' => 'video/mp4', // Assuming standardized to mp4 in Phase 4
            'Content-Disposition' => 'inline',
            'Cache-Control' => 'max-age=2592000, public',
            'Expires' => gmdate('D, d M Y H:i:s', time() + 2592000) . ' GMT',
            'Last-Modified' => $time,
            'Accept-Ranges' => 'bytes',
        ];

        $range = $request->header('Range');

        if ($range) {
            if (preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $range, $matches)) {
                $start = intval($matches[1]);
                if (!empty($matches[2])) {
                    $end = intval($matches[2]);
                }
            }

            // Validasi RFC 7233: Jika start melebihi ukuran file, kembalikan 416 Range Not Satisfiable
            if ($start >= $size || $end < $start) {
                $headers['Content-Range'] = "bytes */{$size}";
                return new StreamedResponse(function () use ($fm) {
                    fclose($fm);
                }, 416, $headers);
            }

            // Mencegah $end melebihi ukuran file (clamp to file boundary)
            $end = min($end, $size - 1);
            $status = 206; // Partial Content
            $headers['Content-Range'] = "bytes {$start}-{$end}/{$size}";
        }

        $length = $end - $start + 1;
        $headers['Content-Length'] = $length;
        
        fseek($fm, $start);

        return new StreamedResponse(function () use ($fm, $length) {
            $bufferSize = 8192; // 8KB chunks
            $bytesSent = 0;

            while (!feof($fm) && $bytesSent < $length && (connection_status() == 0)) {
                $bytesToRead = min($bufferSize, $length - $bytesSent);
                $data = fread($fm, $bytesToRead);
                if ($data === false) {
                    break;
                }
                echo $data;
                flush();
                $bytesSent += strlen($data);
            }

            fclose($fm);
        }, $status, $headers);
    }
}
