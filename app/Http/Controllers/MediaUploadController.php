<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMediaRequest;
use App\Services\MediaService;
use Illuminate\Http\JsonResponse;

class MediaUploadController extends Controller
{
    protected MediaService $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    /**
     * Handle the incoming video upload.
     */
    public function store(StoreMediaRequest $request): JsonResponse
    {
        $file = $request->file('video');
        
        $media = $this->mediaService->processUpload($file);

        return response()->json([
            'message' => 'Video uploaded and processing started.',
            'media_id' => $media->id,
            'status' => $media->status
        ], 202);
    }
}
