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
            <div class="flex-none h-[55vh] p-6 lg:p-8 flex flex-col items-center justify-center bg-[#111111] overflow-hidden relative">
                {{-- Subtle grid background --}}
                <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff05_1px,transparent_1px),linear-gradient(to_bottom,#ffffff05_1px,transparent_1px)] bg-[size:4rem_4rem]"></div>

                <div class="relative w-full max-w-4xl h-full max-h-full aspect-video bg-black rounded-3xl shadow-2xl overflow-hidden
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
            <div class="flex-none h-[30vh] min-h-[240px] bg-white border-t border-gray-200 flex flex-col relative z-10 shrink-0">
                {{-- Timecode bar --}}
                <div class="h-10 border-b border-gray-100 flex items-center justify-between px-8
                            text-xs font-mono font-bold text-gray-400 bg-gray-50 shrink-0">
                    <span id="time-start" class="text-black">00:00.00</span>
                    <span id="time-end">{{ gmdate("i:s.v", $media->duration ?? 0) }}</span>
                </div>

                {{-- Track area --}}
                <div class="flex-1 overflow-hidden relative p-8 flex flex-col justify-center">
                    <div class="h-24 bg-gray-100 rounded-xl border border-gray-200 relative flex w-full"
                         id="visual-timeline">

                        {{-- Thumbnails (JS-injected) --}}
                        <div class="flex h-full w-full overflow-hidden rounded-xl opacity-80"
                             id="timeline-thumbnails"></div>

                        {{-- Selected range --}}
                        <div class="absolute top-0 bottom-0 bg-lime-300/20 border-y-4 border-lime-400 z-10 pointer-events-none"
                             id="timeline-selected"></div>

                        {{-- Trim handles --}}
                        <div class="absolute top-0 bottom-0 w-6 -ml-3 bg-black rounded-full z-20 cursor-ew-resize
                                    flex items-center justify-center hover:scale-x-125 transition-transform shadow-md"
                             id="trim-left">
                            <div class="w-1 h-6 bg-white/50 rounded-full"></div>
                        </div>
                        <div class="absolute top-0 bottom-0 w-6 -mr-3 bg-black rounded-full z-20 cursor-ew-resize
                                    flex items-center justify-center hover:scale-x-125 transition-transform shadow-md"
                             id="trim-right">
                            <div class="w-1 h-6 bg-white/50 rounded-full"></div>
                        </div>

                        {{-- Playhead --}}
                        <div class="absolute -top-4 -bottom-4 w-px bg-red-500 z-30 pointer-events-none shadow-[0_0_8px_rgba(239,68,68,0.5)]"
                             id="playhead">
                            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-4 h-4 bg-red-500 rounded-sm rotate-45"></div>
                        </div>
                    </div>
                </div>
            </div>

        </section>

        {{-- RIGHT: INSPECTOR SIDEBAR --}}
        <aside class="flex-none w-full lg:w-80 bg-[#FCFCFC] border-t lg:border-t-0 lg:border-l border-gray-200 flex flex-col z-20 h-full">

            {{-- Sidebar Header --}}
            <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between shrink-0">
                <div class="flex flex-col gap-0.5 min-w-0">
                    <h2 class="text-lg font-bold text-black truncate" title="{{ $media->original_filename }}">{{ $media->original_filename }}</h2>
                </div>
            </div>

            {{-- Settings content --}}
            <div class="px-6 py-6 flex-1 space-y-8 overflow-y-auto">

                {{-- Primary Actions --}}
                <div class="flex flex-col gap-2">
                    <button class="btn bg-lime-300 text-black hover:bg-lime-400 border border-lime-400 rounded-xl font-bold w-full shadow-sm gap-2 min-h-[2.75rem] h-11"
                            id="btn-save-project">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Export Video
                    </button>
                    <div class="flex gap-2 w-full">
                        <button class="btn btn-ghost border border-gray-200 hover:bg-gray-100 font-bold flex-1 text-gray-500 hover:text-black rounded-xl min-h-[2.75rem] h-11 text-sm"
                                id="btn-save-draft">Save</button>
                        <button class="btn btn-ghost hover:bg-red-50 hover:text-red-600 font-bold flex-1 text-gray-500 rounded-xl min-h-[2.75rem] h-11 text-sm"
                                id="btn-reset">Reset</button>
                    </div>
                </div>

                <div class="h-px w-full bg-gray-200"></div>

                {{-- Video Information --}}
                <div class="space-y-3">
                    <h3 class="text-[10px] font-extrabold uppercase text-gray-400 tracking-wider">Video Information</h3>
                    <div class="text-sm space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 font-medium">Duration</span>
                            <span class="text-black font-bold">
                                {{ gmdate("i:s.v", $media->duration ?? 0) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 font-medium">Resolution</span>
                            <span class="text-black font-bold">1080p</span>
                        </div>
                    </div>
                </div>

                <div class="h-px w-full bg-gray-200"></div>

                {{-- Audio Tools --}}
                <div class="space-y-3">
                    <h3 class="text-[10px] font-extrabold uppercase text-gray-400 tracking-wider">Audio</h3>
                    <div class="flex items-center justify-between">
                        <label for="ctrl-mute" class="text-sm font-semibold cursor-pointer text-gray-700">Mute Track</label>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="ctrl-mute" class="sr-only peer">
                            <div class="w-10 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer
                                        peer-checked:after:translate-x-full peer-checked:after:border-white
                                        after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                        after:bg-white after:border-gray-300 after:border after:rounded-full
                                        after:h-5 after:w-5 after:transition-all peer-checked:bg-[#111111]"></div>
                        </label>
                    </div>
                </div>

                <div class="h-px w-full bg-gray-200"></div>

                {{-- Playback Speed --}}
                <div class="space-y-3">
                    <h3 class="text-[10px] font-extrabold uppercase text-gray-400 tracking-wider">Speed</h3>
                    <select id="ctrl-speed"
                            class="select w-full bg-gray-50 border-gray-200 text-black font-semibold h-10 min-h-10 rounded-xl
                                   focus:border-black focus:ring-0 outline-none text-sm">
                        <option value="0.5">0.5x Slow</option>
                        <option value="0.75">0.75x</option>
                        <option value="1" selected>1.0x Normal</option>
                        <option value="1.25">1.25x</option>
                        <option value="1.5">1.5x Fast</option>
                        <option value="2">2.0x Faster</option>
                    </select>
                </div>

            </div>

            {{-- Sticky Footer: Back Button --}}
            <div class="p-4 border-t border-gray-200 shrink-0">
                <a href="{{ route('workspace') }}"
                   class="flex items-center justify-center gap-2 w-full py-3 bg-white border border-gray-200
                          hover:border-black text-black font-bold rounded-xl transition-all shadow-sm text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    Workspace
                </a>
            </div>

        </aside>

    </div>
</div>
@endsection
