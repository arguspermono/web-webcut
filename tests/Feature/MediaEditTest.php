<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\MediaEdit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use App\Jobs\ProcessEditJob;

class MediaEditTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Queue::fake();
    }

    private function makeReadyMedia(): Media
    {
        return Media::create([
            'id'                => \Illuminate\Support\Str::uuid(),
            'original_filename' => 'test.mp4',
            'storage_path'      => 'media/transcoded/test.mp4',
            'status'            => 'ready',
            'mime_type'         => 'video/mp4',
            'size_bytes'        => 1024000,
        ]);
    }

    /**
     * Valid edit payload should dispatch ProcessEditJob and return 202.
     */
    public function test_valid_edit_dispatches_job(): void
    {
        $media = $this->makeReadyMedia();

        $response = $this->post('/media/edit', [
            'media_id'   => $media->id,
            'start_time' => 2.5,
            'end_time'   => 10.0,
        ]);

        $response->assertStatus(202);
        $response->assertJsonStructure(['message', 'media_edit_id', 'status']);
        Queue::assertPushed(ProcessEditJob::class);
    }

    /**
     * Edit on non-ready media should return 400.
     */
    public function test_edit_on_non_ready_media_returns_400(): void
    {
        $media = Media::create([
            'id'                => \Illuminate\Support\Str::uuid(),
            'original_filename' => 'test.mp4',
            'storage_path'      => 'media/original/test.mp4',
            'status'            => 'processing',
            'mime_type'         => 'video/mp4',
            'size_bytes'        => 1024000,
        ]);

        $response = $this->post('/media/edit', [
            'media_id'   => $media->id,
            'start_time' => 2.5,
            'end_time'   => 10.0,
        ]);

        $response->assertStatus(400);
    }

    /**
     * end_time must be greater than start_time.
     */
    public function test_end_time_must_be_after_start_time(): void
    {
        $media = $this->makeReadyMedia();

        $response = $this->post('/media/edit', [
            'media_id'   => $media->id,
            'start_time' => 15.0,
            'end_time'   => 5.0,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['end_time']);
    }

    /**
     * Poll status returns correct structure when processing.
     */
    public function test_edit_status_polling(): void
    {
        $media = $this->makeReadyMedia();
        $edit  = MediaEdit::create([
            'media_id'    => $media->id,
            'edit_params' => ['start_time' => 0, 'end_time' => 5],
            'status'      => 'processing',
        ]);

        $response = $this->get("/media/edit/{$edit->id}");

        $response->assertStatus(200);
        $response->assertJson(['id' => $edit->id, 'status' => 'processing']);
    }
}
