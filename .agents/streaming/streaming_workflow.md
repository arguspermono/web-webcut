# WebCut Streaming Workflow

## Progressive Download with Range Requests

### 1. Request Initiation
Browser requests a video file from `/stream/{media_id}`.
Example Headers:
`Range: bytes=0-`

### 2. Validation
`StreamController` checks if the media exists and is in the `ready` state.

### 3. File Initialization
`StreamingService` opens the file in binary read mode (`rb`).
Calculates total file size.

### 4. Range Parsing
Service parses the `Range` header.
- Extracts `start` and `end` byte positions.
- If no `end` is provided, sets it to (file size - 1) or a chunk size limit (e.g., 2MB).
- Calculates the `Content-Length` (end - start + 1).

### 5. Sending Headers
Server responds with `206 Partial Content`.
Headers sent:
```
Content-Type: video/mp4
Content-Length: <length of chunk>
Content-Range: bytes <start>-<end>/<total_size>
Accept-Ranges: bytes
```

### 6. Streaming the Buffer
- `fseek` to the `start` position.
- Loop `fread` in small chunks (e.g., 8192 bytes) and `echo` to the output buffer until the `end` position is reached.
- Cleanly `fclose` the file.
