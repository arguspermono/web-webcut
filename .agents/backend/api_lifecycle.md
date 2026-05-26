# WebCut API Lifecycle

## Example: File Upload API (`POST /api/media/upload`)

1. **Client (Browser):** User selects a file and Dropzone.js sends a `multipart/form-data` AJAX request.
2. **Router (`api.php`):** Routes request to `MediaUploadController@store`.
3. **Form Request (`StoreMediaRequest`):** 
   - Validates MIME type (video/mp4, video/quicktime, etc.).
   - Validates max file size.
   - If invalid, returns 422 JSON response.
4. **Controller (`MediaUploadController`):**
   - Receives validated data.
   - Passes the `UploadedFile` object to `MediaService`.
5. **Service (`MediaService`):**
   - Generates a unique secure filename (UUID).
   - Moves the file to `storage/app/public/media/original`.
   - Creates a new record in the `media` database table (status: `uploading`).
   - Dispatches `InitialTranscodeJob`.
   - Updates status to `processing`.
6. **Controller:** Returns 202 Accepted response with the new Media UUID.
7. **Client:** Receives UUID, starts polling for status updates or subscribes to a WebSocket event.
