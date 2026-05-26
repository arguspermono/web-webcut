# WebCut Database Architecture

## Core Schema Requirements

### 1. `media` Table
Stores all information about uploaded and processed videos.
- `id`: Primary key (UUID preferred for security).
- `user_id`: (Optional, if auth is implemented).
- `original_filename`: User's original file name.
- `storage_path`: Path to the standardized MP4 file.
- `thumbnail_path`: Path to the generated thumbnail.
- `status`: Enum (`uploading`, `processing`, `ready`, `failed`).
- `duration`: Float (seconds).
- `resolution`: String (e.g., "1920x1080").
- `mime_type`: String.
- `size_bytes`: BigInteger.
- `created_at`, `updated_at`.

### 2. `media_edits` Table (Optional but recommended)
Tracks derivative versions of a media file.
- `id`: Primary Key.
- `parent_media_id`: Foreign key to `media` table.
- `storage_path`: Path to the edited output file.
- `edit_parameters`: JSON (Stores the exact parameters used, e.g., trim times, crop dims).
- `status`: Enum (`processing`, `ready`, `failed`).
- `created_at`, `updated_at`.

### 3. `jobs` / `failed_jobs` Tables
Standard Laravel queue tables to track asynchronous processing.
