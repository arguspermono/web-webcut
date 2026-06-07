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
<body class="bg-neutral-950 text-neutral-100 font-sans min-h-screen flex flex-col">
    <!-- Header -->
    <header class="flex justify-between items-center px-6 py-4 border-b border-neutral-800 bg-black/50 backdrop-blur-md">
        <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg font-semibold bg-neutral-800/50 border border-neutral-700 hover:bg-neutral-700 transition-colors">&larr; Back to Dashboard</a>
        <h1 class="text-xl font-extrabold bg-gradient-to-br from-violet-500 to-blue-500 bg-clip-text text-transparent tracking-tight">
            Editing: {{ $media->original_filename }}
        </h1>
        <div class="flex gap-3">
            <button class="px-4 py-2 rounded-lg font-semibold bg-neutral-800/50 border border-neutral-700 hover:bg-neutral-700 transition-colors text-neutral-300" id="btn-reset">Reset Changes</button>
            <button class="px-5 py-2 rounded-lg font-semibold text-white bg-gradient-to-br from-violet-600 to-blue-600 hover:opacity-90 transition-opacity shadow-lg shadow-violet-500/20" id="btn-save-project">Process Video</button>
        </div>
    </header>

    <!-- Main Workspace -->
    <main class="flex-1 flex gap-6 p-6 max-w-[1400px] w-full mx-auto">
        <!-- Left Column: Video & Timeline -->
        <div class="flex-1 flex flex-col gap-6 min-w-0">
            <!-- Video Player -->
            <div class="relative w-full rounded-2xl overflow-hidden bg-black shadow-2xl ring-1 ring-white/10 group flex items-center justify-center cursor-pointer" id="video-container">
                <!-- <video id="video-player" class="w-full max-h-[60vh] aspect-video object-contain" preload="auto" playsinline src="{{ asset('storage/' . $media->storage_path) }}">
                    <p>Your browser does not support HTML5 video.</p>
                </video> -->
                <video
                    id="video-player"
                    class="w-full max-h-[60vh] aspect-video object-contain"
                    preload="metadata"
                    playsinline
                    src="{{ route('media.stream', $media) }}"
                ><p>Your browser does not support HTML5 video.</p></video>
                <!-- Play Overlay -->
                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/30 pointer-events-none">
                    <button class="w-16 h-16 rounded-full bg-violet-600/90 backdrop-blur-sm text-white flex items-center justify-center hover:scale-110 hover:bg-violet-500 transition-all pointer-events-auto shadow-2xl ring-4 ring-white/10" id="btn-play-pause">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor" id="icon-play-pause">
                            <polygon points="5 3 19 12 5 21 5 3"></polygon>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Timeline -->
            <div class="bg-neutral-900 p-6 rounded-2xl border border-neutral-800 shadow-xl">
                <div class="flex justify-between mb-4 font-mono text-sm text-neutral-400">
                    <span id="time-start" class="bg-neutral-950 px-3 py-1.5 rounded-lg border border-neutral-800 shadow-inner">00:00.00</span>
                    <span id="time-end" class="bg-neutral-950 px-3 py-1.5 rounded-lg border border-neutral-800 shadow-inner">{{ gmdate("i:s.v", $media->duration ?? 0) }}</span>
                </div>
                
                <!-- Track -->
                <div class="relative h-24 bg-neutral-950 rounded-xl ring-1 ring-white/10 select-none mt-2" id="visual-timeline">
                    <!-- Thumbnails Container -->
                    <div class="flex h-full w-full overflow-hidden rounded-xl" id="timeline-thumbnails">
                        <!-- Thumbnails injected via JS -->
                    </div>
                    
                    <!-- Trim Handles -->
                    <div class="absolute top-0 bottom-0 w-5 -ml-2.5 bg-neutral-200 rounded-md z-20 cursor-ew-resize shadow-[0_0_20px_rgba(0,0,0,0.9)] flex items-center justify-center border-r-[3px] border-violet-500 hover:bg-white hover:scale-x-110 transition-all" id="trim-left">
                        <div class="w-0.5 h-8 bg-neutral-800 rounded-full"></div>
                    </div>
                    <div class="absolute top-0 bottom-0 w-5 -mr-2.5 bg-neutral-200 rounded-md z-20 cursor-ew-resize shadow-[0_0_20px_rgba(0,0,0,0.9)] flex items-center justify-center border-l-[3px] border-violet-500 hover:bg-white hover:scale-x-110 transition-all" id="trim-right">
                        <div class="w-0.5 h-8 bg-neutral-800 rounded-full"></div>
                    </div>
                    
                    <!-- Selected Range -->
                    <div class="absolute top-0 bottom-0 bg-violet-500/30 border-y-2 border-violet-500 shadow-[inset_0_0_20px_rgba(139,92,246,0.3)] z-10 pointer-events-none" id="timeline-selected"></div>
                    
                    <!-- Playhead -->
                    <div class="absolute -top-4 -bottom-4 w-0.5 bg-red-500 z-30 pointer-events-none drop-shadow-[0_0_8px_rgba(239,68,68,0.8)]" id="playhead">
                        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-4 h-4 bg-red-500 rounded-full shadow-lg shadow-red-500/50 flex items-center justify-center">
                            <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <aside class="w-80 shrink-0 bg-neutral-900 p-6 rounded-2xl border border-neutral-800 flex flex-col gap-6 shadow-xl h-fit sticky top-6">
            <div class="pb-4 border-b border-neutral-800 flex items-center gap-2">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-violet-500"><path d="M12 20V10M18 20V4M6 20v-4"/></svg>
                <h3 class="text-lg font-bold text-white">Editor Settings</h3>
            </div>
            
            <!-- Video Info -->
            <div>
                <h4 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-3">Video Information</h4>
                <div class="bg-neutral-950 rounded-xl p-4 border border-neutral-800/50 space-y-3 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-neutral-400">Duration</span>
                        <span class="font-mono text-neutral-200 bg-neutral-900 px-2 py-0.5 rounded border border-neutral-800">{{ gmdate("i:s.v", $media->duration ?? 0) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-neutral-400">Resolution</span>
                        <span class="font-mono text-neutral-200 bg-neutral-900 px-2 py-0.5 rounded border border-neutral-800">1080p</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-neutral-400">Source</span>
                        <span class="text-neutral-200 truncate ml-4" title="{{ $media->original_filename }}">{{ $media->original_filename }}</span>
                    </div>
                </div>
            </div>

            <!-- Audio -->
            <div>
                <h4 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-3">Audio Tools</h4>
                <div class="bg-neutral-950 rounded-xl p-4 border border-neutral-800/50 hover:border-neutral-700 transition-colors">
                    <div class="flex items-center justify-between">
                        <label for="ctrl-mute" class="text-sm font-semibold text-neutral-200 cursor-pointer flex items-center gap-2">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 5L6 9H2v6h4l5 4V5z"/><line x1="23" y1="9" x2="17" y2="15"/><line x1="17" y1="9" x2="23" y2="15"/></svg>
                            Mute Track
                        </label>
                        <!-- Custom Tailwind Toggle -->
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="ctrl-mute" class="sr-only peer">
                            <div class="w-11 h-6 bg-neutral-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-violet-600"></div>
                        </label>
                    </div>
                    <p class="text-[11px] text-neutral-500 mt-2">Strips audio during final render.</p>
                </div>
            </div>

            <!-- Playback Speed -->
            <div>
                <h4 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-3">Playback Speed</h4>
                <div class="bg-neutral-950 rounded-xl p-4 border border-neutral-800/50 hover:border-neutral-700 transition-colors">
                    <div class="relative">
                        <select id="ctrl-speed" class="w-full bg-neutral-900 text-neutral-200 font-medium text-sm rounded-lg border border-neutral-700 focus:ring-violet-500 focus:border-violet-500 block p-3 outline-none cursor-pointer appearance-none">
                            <option value="0.5">0.5x Slow</option>
                            <option value="0.75">0.75x</option>
                            <option value="1" selected>1.0x Normal</option>
                            <option value="1.25">1.25x</option>
                            <option value="1.5">1.5x Fast</option>
                            <option value="2">2.0x Faster</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-neutral-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
    </main>
</body>
</html>
