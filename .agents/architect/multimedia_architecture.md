# WebCut Multimedia Architecture

## Pipeline Overview
The multimedia pipeline is centered around FFmpeg/FFprobe and is triggered via asynchronous jobs to avoid blocking the web server.

### 1. Ingestion
- User uploads video.
- File is saved to temporary storage.
- An `InitialTranscodeJob` is dispatched.

### 2. Standardization (Transcoding)
- ALL videos must be converted to a standard format for predictable behavior in the browser and streaming systems.
- **Target Format:** MP4 container
- **Video Codec:** H.264 (`libx264`)
- **Pixel Format:** YUV420p (broadest compatibility)
- **Presets:** `medium` or `fast` with CRF 23.
- **Flags:** `+faststart` (moves moov atom to the front for immediate streaming).
- *Command:* `ffmpeg -i input.ext -c:v libx264 -preset medium -crf 23 -pix_fmt yuv420p -movflags +faststart output.mp4`

### 3. Metadata & Asset Extraction
- **FFprobe:** Used to extract duration, resolution, codec info.
- **Thumbnails:** Extracted from the middle of the video or at keyframes using FFmpeg.

### 4. Editing Pipeline
- The editor sends an array of operations (e.g., trim start/end, crop coordinates, mute boolean, speed multiplier).
- The `FFmpegService` parses this array and constructs a *single* optimized FFmpeg command using complex filtergraphs (`-filter_complex`).
- This approach avoids re-encoding the video multiple times, preserving quality and saving compute time.
- Original files are strictly preserved; edits are saved as new derivative files.
