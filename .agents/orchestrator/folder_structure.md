# WebCut Expected Folder Structure

## Architecture Enforced

The following directory structure must be strictly adhered to:

```
.agents/
  planner/
  architect/
  backend/
  ffmpeg/
  streaming/
  frontend/
  security/
  testing/
  docs/
  orchestrator/
app/
  Http/
    Controllers/
      Api/
        MediaUploadController.php
        EditorAPIController.php
      StreamController.php
    Requests/
      StoreMediaRequest.php
      ProcessEditRequest.php
  Services/
    MediaService.php
    FFmpegService.php
    StreamingService.php
  Jobs/
    InitialTranscodeJob.php
    ExtractThumbnailJob.php
    ApplyVideoEditsJob.php
  Models/
    Media.php
    MediaEdit.php
database/
  migrations/
resources/
  views/
    layouts/
      app.blade.php
    media/
      upload.blade.php
      editor.blade.php
      watch.blade.php
  js/
    editor.js
    upload.js
routes/
  web.php
  api.php
storage/
  app/
    public/
      media/
        original/
        transcoded/
        thumbnails/
        edits/
```
