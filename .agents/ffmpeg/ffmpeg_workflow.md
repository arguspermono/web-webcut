# WebCut FFMPEG Workflow

## 1. Initial Transcode (Standardization)
Every uploaded video must be standardized before it can be streamed or edited reliably.

**Input:** Any uploaded format (e.g., .mov, .avi, unoptimized .mp4).
**Output:** Web-optimized MP4.
**Process:**
- Ensure it is H.264 video, AAC audio.
- Enforce YUV420p pixel format.
- Ensure the MOOV atom is at the beginning (`-movflags +faststart`).
- Scale down if the resolution is unnecessarily high (e.g., cap at 1080p to save processing time).

## 2. Thumbnail Extraction
**Process:**
- Run FFmpeg to capture a single frame (`-vframes 1`).
- Target a specific timestamp (e.g., `00:00:05` or halfway through the duration).
- Output as JPG.

## 3. Complex Edits (The Editor)
When a user submits an edit (trim, crop, mute), we combine them into a single command using `filter_complex`.

**Example Workflow:**
- User wants to trim from 00:05 to 00:15, and crop a 500x500 box at x:100, y:100.
- `FFmpegService` translates this into:
  - Trim: `-ss 00:00:05 -to 00:00:15`
  - Crop: `-vf "crop=500:500:100:100"`
- Combine into a single command.
- Execute command synchronously in a Job queue, writing to a new file in `storage/app/public/media/edits`.

## Security Considerations
- All dynamic inputs (trim times, crop coordinates, filenames) MUST be escaped and validated.
- Use the `Symfony\Component\Process\Process` component to run commands safely and avoid shell injection.
