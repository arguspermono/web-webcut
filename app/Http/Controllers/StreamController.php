<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\MediaEdit;
use App\Services\StreamingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StreamController extends Controller
{
    protected StreamingService $streamingService;

    public function __construct(StreamingService $streamingService)
    {
        $this->streamingService = $streamingService;
    }

    /**
     * Handle the incoming request to stream a media file.
     */
    public function show(Media $media, Request $request)
    {
        Log::info('STREAM HIT', [
            'media_id' => $media->id,
            'range' => $request->header('Range')
        ]);

        if ($media->status !== 'ready') {
            return response()->json([
                'message' => 'Media not ready for streaming'
            ], 400);
        }

        return $this->streamingService->stream($media, $request);
    }

    /**
     * Display the streaming watch page.
     */
    public function watch(Media $media)
    {
        if ($media->status !== 'ready') {
            return redirect()->route('dashboard')->with('error', 'Media not ready for streaming');
        }

        return view('stream', compact('media'));
    }

    /**
     * Handle the incoming request to stream an edited media file.
     */
    public function showEdit(MediaEdit $mediaEdit, Request $request)
    {
        Log::info('STREAM EDIT HIT', [
            'media_edit_id' => $mediaEdit->id,
            'range' => $request->header('Range')
        ]);

        if ($mediaEdit->status !== 'ready' || !$mediaEdit->output_path) {
            return response()->json([
                'message' => 'Edited media not ready for streaming'
            ], 400);
        }

        return $this->streamingService->streamPath($mediaEdit->output_path, $request);
    }
}
