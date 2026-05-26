<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebCut - Premium Video Editor</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <header class="app-header">
        <h1>WebCut.</h1>
    </header>

    <main class="workspace">
        
        <!-- Upload Area -->
        <div class="upload-zone" id="upload-zone">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="url(#gradient)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <defs>
                    <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#8b5cf6" />
                        <stop offset="100%" stop-color="#3b82f6" />
                    </linearGradient>
                </defs>
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="17 8 12 3 7 8"></polyline>
                <line x1="12" y1="3" x2="12" y2="15"></line>
            </svg>
            <p>Drag and drop your video here to start</p>
            <p style="font-size: 0.9rem; color: #64748b; margin-top: 0.5rem;">MP4, WebM, MOV (Max 500MB)</p>
            <div class="loader" id="upload-loader">Uploading and processing...</div>
            <input type="file" id="file-input" accept="video/mp4,video/webm,video/quicktime">
        </div>

        <!-- Editor Area -->
        <div class="editor-zone" id="editor-zone">
            
            <div class="player-wrapper">
                <video id="video-player" class="video-js vjs-big-play-centered vjs-theme-city" controls preload="auto">
                    <p class="vjs-no-js">
                        To view this video please enable JavaScript, and consider upgrading to a web browser that supports HTML5 video
                    </p>
                </video>
            </div>

            <div class="timeline-wrapper">
                <div class="timeline-header">
                    <span id="time-start">00:00.00</span>
                    <span id="time-end">00:00.00</span>
                </div>
                <div id="timeline-slider"></div>
            </div>

            <div class="editor-controls">
                <button class="btn-primary" id="btn-trim">Trim Video</button>
            </div>
            
            <input type="hidden" id="current-media-id">

        </div>
    </main>
</body>
</html>
