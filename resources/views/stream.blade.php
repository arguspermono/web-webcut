<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebCut Lite - Watch</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="watch-body">
    <header class="app-header">
        <a href="{{ route('dashboard') }}" class="btn-secondary">&larr; Back to Dashboard</a>
        <h1>Watch: {{ $media->original_filename }}</h1>
    </header>

    <main class="watch-workspace">
        <div class="player-wrapper">
            <video id="video-player" class="video-js vjs-big-play-centered vjs-theme-city" controls preload="auto" data-setup='{}'>
                <!-- We stream via the stream controller route to leverage HTTP range requests natively configured there -->
                <source src="{{ route('media.stream', $media->id) }}" type="{{ $media->mime_type ?? 'video/mp4' }}">
                <p class="vjs-no-js">
                    To view this video please enable JavaScript, and consider upgrading to a web browser that supports HTML5 video
                </p>
            </video>
        </div>
        
        <div class="metadata-panel">
            <h2>Metadata</h2>
            <ul>
                <li><strong>Resolution:</strong> {{ $media->resolution }}</li>
                <li><strong>Duration:</strong> {{ gmdate("H:i:s", $media->duration ?? 0) }}</li>
                <li><strong>Size:</strong> {{ number_format(($media->size_bytes ?? 0) / 1048576, 2) }} MB</li>
            </ul>
            <a href="{{ route('media.stream', $media->id) }}" download="{{ $media->original_filename }}" class="btn-primary">Download Final Video</a>
        </div>
    </main>
</body>
</html>
