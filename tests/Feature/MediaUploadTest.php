<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Jobs\InitialTranscodeJob;

class MediaUploadTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Queue::fake();
    }

    /**
     * A valid video upload should return 202 and dispatch a transcode job.
     */
    public function test_valid_video_upload_dispatches_job(): void
    {
        $file = UploadedFile::fake()->create('test-video.mp4', 1024, 'video/mp4');

        $response = $this->post('/media/upload', [
            'video' => $file,
        ]);

        $response->assertStatus(202);
        $response->assertJsonStructure(['message', 'media_id', 'status']);
        Queue::assertPushed(InitialTranscodeJob::class);
    }

    /**
     * Uploading a non-video file should be rejected with 422.
     */
    public function test_non_video_upload_is_rejected(): void
    {
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->post('/media/upload', [
            'video' => $file,
        ]);

        $response->assertStatus(422);
    }

    /**
     * Uploading without a file should fail validation.
     */
    public function test_missing_file_fails_validation(): void
    {
        $response = $this->post('/media/upload', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['video']);
    }
}
