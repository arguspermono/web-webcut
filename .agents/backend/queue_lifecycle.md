# WebCut Queue Lifecycle

## Processing Asynchronous Multimedia Tasks

### 1. Dispatch
Jobs are typically dispatched from a Service class. 
`InitialTranscodeJob::dispatch($mediaModel);`

### 2. Payload Serialization
The Job serializes the Eloquent Model (`$mediaModel`).

### 3. Execution (Worker)
The Queue Worker (`php artisan queue:work`) picks up the job.
- The `handle()` method is invoked.
- The Job instantiates the `FFmpegService`.
- It executes the heavy processing.

### 4. Status Updates
During the job, the worker may update the `media` table (e.g., progress percentage).
Upon success:
- Status changed to `ready`.
- New file paths (transcoded video, thumbnail) are saved to DB.
Upon failure:
- Status changed to `failed`.
- Error is logged.
- The job may be retried automatically depending on configuration.

### 5. Notification
(Optional) Fire an event `MediaProcessingCompleted` which can broadcast to the frontend via WebSockets.
