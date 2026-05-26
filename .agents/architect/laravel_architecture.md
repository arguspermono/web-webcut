# WebCut Laravel Architecture

## Design Pattern: Service-Oriented Monolith

### 1. Controllers
*Thin Controllers:* Controllers are responsible ONLY for receiving HTTP requests, returning HTTP responses (or Views), and passing validated data to Services.
*Example:* `MediaUploadController`, `StreamController`, `EditorAPIController`.

### 2. Form Requests
*Strict Validation:* All incoming payloads (especially video files and FFMPEG parameters) must be validated via Form Requests to prevent shell injection and ensure data integrity.
*Example:* `StoreMediaRequest`, `ProcessEditRequest`.

### 3. Services
*Core Business Logic:* All heavy lifting is done here. FFMPEG command generation, file path resolution, and chunked streaming logic live in dedicated service classes.
*Example:* `FFmpegService`, `StreamingService`, `MediaService`.

### 4. Jobs (Queues)
*Asynchronous Execution:* Any task that takes more than a few seconds (like transcoding) must be pushed to a queue.
*Example:* `TranscodeVideoJob`, `ExtractThumbnailJob`, `ApplyVideoEditsJob`.

### 5. Middleware & Security
*Hardening:* Custom middleware or strict route definitions to prevent path traversal when accessing media files.

### 6. Events & Listeners (Optional)
*Decoupling:* For triggering notifications or updating database statuses when a long-running video job completes.
