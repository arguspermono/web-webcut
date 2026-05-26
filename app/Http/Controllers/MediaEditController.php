<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMediaEditRequest;
use App\Jobs\ProcessEditJob;
use App\Models\Media;
use App\Models\MediaEdit;
use Illuminate\Http\JsonResponse;

class MediaEditController extends Controller
{
    /**
     * Accept an edit payload, persist it, and dispatch the processing job.
     */
    public function store(StoreMediaEditRequest $request): JsonResponse
    {
        $media = Media::findOrFail($request->input('media_id'));

        if ($media->status !== 'ready') {
            return response()->json(['message' => 'Media is not ready for editing.'], 400);
        }

        $mediaEdit = MediaEdit::create([
            'media_id'    => $media->id,
            'edit_params' => $request->only([
                'start_time', 'end_time',
                'crop_w', 'crop_h', 'crop_x', 'crop_y',
                'speed', 'mute',
            ]),
            'status' => 'processing',
        ]);

        ProcessEditJob::dispatch($mediaEdit, $media);

        return response()->json([
            'message'      => 'Edit queued successfully.',
            'media_edit_id' => $mediaEdit->id,
            'status'       => $mediaEdit->status,
        ], 202);
    }

    /**
     * Poll the status and return a download URL when ready.
     */
    public function show(MediaEdit $mediaEdit): JsonResponse
    {
        $data = [
            'id'     => $mediaEdit->id,
            'status' => $mediaEdit->status,
        ];

        if ($mediaEdit->status === 'ready' && $mediaEdit->output_path) {
            $data['download_url'] = asset('storage/' . $mediaEdit->output_path);
        }

        return response()->json($data);
    }
}
