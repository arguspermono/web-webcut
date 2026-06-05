<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\StreamingService;
use Illuminate\Http\Request;

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
        if ($media->status !== 'ready') {
            return response()->json(['message' => 'Media not ready for streaming'], 400);
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
}
