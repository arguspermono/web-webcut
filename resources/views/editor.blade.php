<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebCut Lite - Editor</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        window.WebCutConfig = {
            mediaId: '{{ $media->id }}',
            duration: {{ $media->duration ?? 0 }},
            status: '{{ $media->status }}'
        };
    </script>
</head>
<body class="editor-body">
    <header class="app-header">
        <a href="{{ route('dashboard') }}" class="btn-secondary">&larr; Back to Dashboard</a>
        <h1>Editing: {{ $media->original_filename }}</h1>
        <button class="btn-primary" id="btn-save-project">Save & Process</button>
    </header>

    <main class="editor-workspace layout-with-sidebar">
        <div class="editor-main-column">
            <div class="player-wrapper">
                <video id="video-player" class="native-video-preview" preload="auto" src="{{ asset('storage/' . $media->storage_path) }}">
                    <p>Your browser does not support HTML5 video.</p>
                </video>
                <div class="player-controls-bar">
                    <button class="btn-control" id="btn-play-pause">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" id="icon-play-pause">
                            <polygon points="5 3 19 12 5 21 5 3"></polygon>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="timeline-wrapper">
                <div class="timeline-header">
                    <span id="time-start">00:00.00</span>
                    <span id="time-end">{{ gmdate("i:s.v", $media->duration ?? 0) }}</span>
                </div>
                
                <div class="visual-timeline" id="visual-timeline">
                    <div class="timeline-thumbnails" id="timeline-thumbnails">
                        <!-- Thumbnails will be injected here via JS -->
                    </div>
                    <div class="trim-handle trim-left" id="trim-left"></div>
                    <div class="trim-handle trim-right" id="trim-right"></div>
                    <div class="timeline-selected-area" id="timeline-selected"></div>
                    <div class="playhead" id="playhead"></div>
                </div>
            </div>
        </div>

        <aside class="editor-side-panel">
            <h3>Editing Settings</h3>
            
            <div class="tool-section">
                <h4>Audio</h4>
                <div class="control-group toggle-group">
                    <label for="ctrl-mute">Mute Track</label>
                    <label class="switch">
                        <input type="checkbox" id="ctrl-mute" class="editor-checkbox">
                        <span class="slider round"></span>
                    </label>
                </div>
                <p class="help-text">Remove audio track during processing</p>
            </div>

            <div class="tool-section">
                <h4>Playback Speed</h4>
                <div class="control-group">
                    <select id="ctrl-speed" class="editor-select full-width">
                        <option value="0.5">0.5x</option>
                        <option value="0.75">0.75x</option>
                        <option value="1" selected>1.0x</option>
                        <option value="1.25">1.25x</option>
                        <option value="1.5">1.5x</option>
                        <option value="2">2.0x</option>
                    </select>
                </div>
            </div>
        </aside>
    </main>
</body>
</html>
