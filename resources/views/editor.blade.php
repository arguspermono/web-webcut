@extends('layouts.app')

@section('title', 'WebCut Lite - Editor')

@push('head')
<script>
    window.WebCutConfig = {
        mediaId: '{{ $media->id }}',
        duration: {{ $media->duration ?? 0 }},
        status: '{{ $media->status }}'
    };
</script>
@endpush

@section('header')
    @include('partials.navbar')
@endsection

@section('content')
<div class="flex flex-col w-full min-h-full">
    {{-- Main Editor Body --}}
    <div class="flex-1 flex flex-col lg:flex-row min-w-0 relative">

        {{-- LEFT: PREVIEW & TIMELINE --}}
        <section class="flex-1 flex flex-col min-w-0">

            {{-- VIDEO PREVIEW --}}
            <div class="flex-none h-[55vh] p-6 lg:p-8 flex flex-col items-center justify-center bg-neutral overflow-hidden relative">
                {{-- Subtle grid background --}}
                <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff05_1px,transparent_1px),linear-gradient(to_bottom,#ffffff05_1px,transparent_1px)] bg-[size:4rem_4rem]"></div>

                <div class="relative w-full max-w-4xl h-full max-h-full aspect-video bg-black rounded-box shadow-2xl overflow-hidden
                            flex items-center justify-center border border-white/10 cursor-pointer group z-10"
                     id="video-container">
                    <video
                        id="video-player"
                        class="w-full h-full object-contain"
                        preload="metadata"
                        playsinline
                        src="{{ route('media.stream', $media) }}"
                    ><p>Your browser does not support HTML5 video.</p></video>

                    {{-- Play overlay --}}
                    <div class="absolute inset-0 flex items-center justify-center
                                opacity-0 group-hover:opacity-100 transition-opacity bg-black/40 pointer-events-none">
                        <button class="btn btn-circle btn-lg bg-lime-300 text-black hover:bg-lime-400
                                       border-none hover:scale-105 transition-transform pointer-events-auto shadow-xl"
                                id="btn-play-pause">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor" id="icon-play-pause">
                                <polygon points="5 3 19 12 5 21 5 3"></polygon>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- TIMELINE --}}
            <div class="flex-none h-[30vh] min-h-[240px] bg-base-100 border-t border-base-200 flex flex-col relative z-10 shrink-0">
                {{-- Timecode bar --}}
                <div class="h-10 border-b border-base-200 flex items-center justify-between px-8
                            text-xs font-mono font-bold text-base-content/50 bg-base-200 shrink-0">
                    <span id="time-start" class="text-base-content">00:00.00</span>
                    <span id="time-end">{{ gmdate("H:i:s", $media->duration ?? 0) }}</span>
                </div>

                {{-- Track area --}}
                <div class="flex-1 overflow-hidden relative p-8 flex flex-col justify-center">
                    <div class="h-24 bg-base-200 rounded-box border border-base-300 relative flex w-full"
                         id="visual-timeline">

                        {{-- Thumbnails (JS-injected) --}}
                        <div class="flex h-full w-full overflow-hidden rounded-box opacity-80"
                             id="timeline-thumbnails"></div>

                        {{-- Selected range --}}
                        <div class="absolute top-0 bottom-0 bg-lime-300/20 border-y-4 border-lime-400 z-10 pointer-events-none"
                             id="timeline-selected"></div>

                        {{-- Trim handles --}}
                        <div class="absolute top-0 bottom-0 w-6 -ml-3 bg-neutral rounded-full z-20 cursor-ew-resize
                                    flex items-center justify-center hover:scale-x-125 transition-transform shadow-md"
                             id="trim-left">
                            <div class="w-1 h-6 bg-white/50 rounded-full"></div>
                        </div>
                        <div class="absolute top-0 bottom-0 w-6 -mr-3 bg-neutral rounded-full z-20 cursor-ew-resize
                                    flex items-center justify-center hover:scale-x-125 transition-transform shadow-md"
                             id="trim-right">
                            <div class="w-1 h-6 bg-white/50 rounded-full"></div>
                        </div>

                        {{-- Playhead --}}
                        <div class="absolute -top-4 -bottom-4 w-px bg-error z-30 pointer-events-none shadow-[0_0_8px_rgba(239,68,68,0.5)]"
                             id="playhead">
                            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-4 h-4 bg-error rounded-sm rotate-45"></div>
                        </div>
                    </div>
                </div>
            </div>

        </section>

        {{-- RIGHT: INSPECTOR SIDEBAR --}}
        <aside class="flex-none w-full lg:w-72 bg-base-100 border-t lg:border-t-0 lg:border-l border-base-200 flex flex-col z-20 h-full">

            {{-- Sidebar Header --}}
            <div class="px-4 py-3 border-b border-base-200 flex flex-col gap-2 shrink-0">
                <a href="{{ route('workspace') }}" class="btn btn-sm btn-ghost rounded-full self-start -ml-2 gap-2 font-bold text-base-content/60 hover:text-base-content hover:bg-base-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    Back to Workspace
                </a>
                <div class="min-w-0">
                    <h2 class="text-sm font-bold text-base-content truncate" title="{{ $media->original_filename }}">{{ $media->original_filename }}</h2>
                    <p class="text-[10px] text-base-content/40 font-medium mt-0.5">{{ $media->created_at->format('M d, Y · H:i') }}</p>
                </div>
            </div>

            {{-- Settings content --}}
            <div class="px-4 py-4 flex-1 flex flex-col gap-4 overflow-y-auto">

                {{-- Primary Actions --}}
                <div class="flex flex-col gap-1.5">
                    <button class="btn btn-sm bg-lime-300 text-black hover:bg-lime-400 border-none rounded-box font-bold w-full shadow-sm gap-2"
                            id="btn-save-project">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Export Video
                    </button>
                    <div class="flex gap-1.5 w-full">
                        <button class="btn btn-sm btn-outline btn-neutral font-bold flex-1 rounded-box" id="btn-save-draft">Save Draft</button>
                        <button class="btn btn-sm btn-outline btn-error font-bold flex-1 rounded-box" id="btn-reset">Reset</button>
                    </div>
                </div>

                <div class="divider my-0"></div>

                {{-- Video Information: compact grid --}}
                <div class="space-y-1.5">
                    <h3 class="text-[10px] font-extrabold uppercase text-base-content/40 tracking-wider">Video Info</h3>
                    <div class="grid grid-cols-2 gap-x-2 gap-y-1 text-xs">
                        <span class="text-base-content/60">Duration</span>
                        <span class="text-base-content font-bold text-right">{{ gmdate("H:i:s", $media->duration ?? 0) }}</span>
                        <span class="text-base-content/60">Resolution</span>
                        <span class="text-base-content font-bold text-right">1080p</span>
                        <span class="text-base-content/60">File Size</span>
                        <span class="text-base-content font-bold text-right">{{ $media->size_bytes ? number_format($media->size_bytes / 1048576, 2) . ' MB' : '—' }}</span>
                    </div>
                </div>

                <div class="divider my-0"></div>

                {{-- Export Settings: side-by-side --}}
                <div class="space-y-1.5">
                    <h3 class="text-[10px] font-extrabold uppercase text-base-content/40 tracking-wider">Export Settings</h3>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="flex flex-col gap-1">
                            <label class="text-[10px] font-semibold text-base-content/60 uppercase tracking-wide">Resolution</label>
                            <select id="ctrl-resolution" class="select select-bordered select-xs w-full font-semibold text-base-content rounded-box focus:outline-none">
                                <option value="original" selected>Original</option>
                                <option value="1080p">1080p</option>
                                <option value="720p">720p</option>
                                <option value="480p">480p</option>
                            </select>
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-[10px] font-semibold text-base-content/60 uppercase tracking-wide">Format</label>
                            <select id="ctrl-format" class="select select-bordered select-xs w-full font-semibold text-base-content rounded-box focus:outline-none">
                                <option value="mp4" selected>MP4</option>
                                <option value="webm">WebM</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="divider my-0"></div>

                {{-- Audio + Speed in one row --}}
                <div class="space-y-1.5">
                    <h3 class="text-[10px] font-extrabold uppercase text-base-content/40 tracking-wider">Playback</h3>
                    <div class="flex items-center justify-between">
                        <label for="ctrl-mute" class="text-xs font-semibold cursor-pointer text-base-content">Mute Audio</label>
                        <input type="checkbox" id="ctrl-mute" class="toggle toggle-xs toggle-neutral" />
                    </div>
                </div>

                <div class="divider my-0"></div>

                <div class="space-y-1.5">
                    <h3 class="text-[10px] font-extrabold uppercase text-base-content/40 tracking-wider">Speed</h3>
                    <div class="join w-full">
                        <button type="button" data-speed="0.5"  class="speed-btn join-item btn btn-xs flex-1 btn-ghost font-bold border border-base-200 hover:btn-neutral">0.5×</button>
                        <button type="button" data-speed="0.75" class="speed-btn join-item btn btn-xs flex-1 btn-ghost font-bold border border-base-200 hover:btn-neutral">0.75×</button>
                        <button type="button" data-speed="1"    class="speed-btn join-item btn btn-xs flex-1 btn-neutral font-bold" id="speed-active">1×</button>
                        <button type="button" data-speed="1.5"  class="speed-btn join-item btn btn-xs flex-1 btn-ghost font-bold border border-base-200 hover:btn-neutral">1.5×</button>
                        <button type="button" data-speed="2"    class="speed-btn join-item btn btn-xs flex-1 btn-ghost font-bold border border-base-200 hover:btn-neutral">2×</button>
                    </div>
                    <select id="ctrl-speed" class="hidden">
                        <option value="0.5">0.5x</option>
                        <option value="0.75">0.75x</option>
                        <option value="1" selected>1x</option>
                        <option value="1.5">1.5x</option>
                        <option value="2">2x</option>
                    </select>
                </div>

            </div>

        </aside>

    </div>
</div>
@endsection
