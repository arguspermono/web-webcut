# WebCut Streaming Architecture

## Concept: Progressive Streaming via HTTP Range Requests

### 1. The Challenge
Standard file serving requires the client to download the entire video before playback begins or prevents seeking to unbuffered parts of the video. 

### 2. The Solution (Byte-Range Requests)
WebCut implements a streaming endpoint that reads the `Range` HTTP header sent by the browser (e.g., Video.js).

- **Client Request:** Browser requests video, asks for bytes `0-` or specific ranges like `bytes=1024-2048`.
- **Server Response:** 
  - The server reads the requested byte range.
  - Responds with HTTP Status `206 Partial Content`.
  - Sets headers:
    - `Content-Range: bytes START-END/TOTAL_SIZE`
    - `Accept-Ranges: bytes`
    - `Content-Length: CHUNK_SIZE`
    - `Content-Type: video/mp4`
  - Streams only that specific chunk of the file back to the client.

### 3. Laravel Implementation
A dedicated `StreamService` will handle the underlying file I/O (using native PHP `fopen`, `fseek`, `fread`) to bypass Laravel's memory limits, ensuring large videos are streamed efficiently without memory exhaustion.

### 4. Dependency on Transcoding
This streaming architecture relies heavily on the `+faststart` movflag applied during the FFMPEG transcoding phase. If the `moov` atom is at the end of the file, the browser cannot begin progressive playback until the entire file is downloaded, completely defeating the purpose of the Range request.
