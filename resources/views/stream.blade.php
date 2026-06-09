@extends('layouts.app')

@section('title', 'Watch Raw Video - WebCut Lite')

@section('header')
    @include('partials.navbar')
@endsection

@section('content')
<div class="flex flex-col w-full overflow-hidden">
    {{-- Main Body --}}
    <div class="flex-1 flex flex-col lg:flex-row min-w-0 overflow-hidden relative">

        {{-- LEFT: VIDEO PREVIEW --}}
        <section class="flex-1 flex flex-col min-w-0 h-full overflow-hidden bg-[#111111]">
            <div class="flex-1 p-6 lg:p-8 flex flex-col items-center justify-center overflow-hidden relative">
                <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff05_1px,transparent_1px),linear-gradient(to_bottom,#ffffff05_1px,transparent_1px)] bg-[size:4rem_4rem]"></div>

                <div class="relative w-full max-w-5xl max-h-[65vh] aspect-video bg-black rounded-3xl shadow-2xl overflow-hidden
                            flex items-center justify-center border border-white/10 z-10"
                     id="video-container">
                    <video
                        controls
                        class="w-full h-full object-contain"
                        preload="metadata"
                        controlsList="nodownload"
                        src="{{ route('media.stream', $media->id) }}"
                    ><p>Your browser does not support HTML5 video.</p></video>
                </div>
            </div>
        </section>

        {{-- RIGHT: INSPECTOR SIDEBAR --}}
        <aside class="flex-none w-full lg:w-[400px] xl:w-[420px] bg-[#FCFCFC] border-t lg:border-t-0 lg:border-l border-gray-200 overflow-y-auto flex flex-col z-20 h-full">

            {{-- Sidebar Header --}}
            <div class="px-6 py-5 border-b border-gray-200 shrink-0">
                <div class="flex flex-col gap-0.5 min-w-0">
                    <h2 class="text-lg font-extrabold text-black truncate" title="{{ $media->original_filename }}">{{ $media->original_filename }}</h2>
                    <div class="flex items-center gap-2">
                        @if($latestEdit)
                            <span class="badge bg-lime-300 text-black border border-lime-400 py-0.5 px-2 rounded uppercase text-[9px] font-bold">Edited</span>
                            <span class="text-[10px] text-gray-400 font-medium">Export complete</span>
                        @else
                            <span class="badge bg-gray-100 text-gray-600 border border-gray-200 py-0.5 px-2 rounded uppercase text-[9px] font-bold">Raw</span>
                            <span class="text-[10px] text-gray-400 font-medium">Original file</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Settings content --}}
            <div class="px-6 py-6 flex-1 space-y-8">

                {{-- Download CTA --}}
                <a href="{{ route('media.stream', $media->id) }}"
                   download="{{ $media->original_filename }}"
                   class="flex items-center justify-center gap-3 w-full py-5 bg-lime-300 hover:bg-lime-400
                          text-black font-extrabold text-lg rounded-[2rem] border border-lime-400 shadow-sm
                          transition-all hover:shadow-md group">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                         class="group-hover:translate-y-0.5 transition-transform">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Download Source
                </a>

                {{-- Video Information --}}
                <div class="space-y-4">
                    <h3 class="text-xs font-extrabold uppercase text-gray-400 tracking-wider">File Information</h3>
                    <div class="bg-white text-sm rounded-3xl border border-gray-200 p-6 space-y-5 shadow-sm">
                        
                        <div class="flex flex-col gap-1.5">
                            <span class="text-gray-400 text-[10px] uppercase tracking-widest font-extrabold">Original Name</span>
                            <span class="font-bold text-black text-base truncate" title="{{ $media->original_filename }}">
                                {{ $media->original_filename }}
                            </span>
                        </div>

                        <div class="h-px w-full bg-gray-100"></div>

                        <div class="flex flex-col gap-1.5">
                            <span class="text-gray-400 text-[10px] uppercase tracking-widest font-extrabold">Uploaded On</span>
                            <span class="font-bold text-black">{{ $media->created_at->format('M d, Y · H:i') }}</span>
                        </div>

                        <div class="h-px w-full bg-gray-100"></div>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 font-semibold">Duration</span>
                            <span class="font-mono bg-gray-50 text-black px-3 py-1.5 rounded-lg border border-gray-200 font-bold">
                                {{ gmdate("i:s.v", $media->duration ?? 0) }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 font-semibold">Size</span>
                            <span class="font-mono bg-gray-50 text-black px-3 py-1.5 rounded-lg border border-gray-200 font-bold">
                                {{ number_format(($media->size_bytes ?? 0) / 1048576, 2) }} MB
                            </span>
                        </div>

                    </div>
                </div>

            </div>

            {{-- Sticky Footer: Back Button --}}
            <div class="p-6 border-t border-gray-200 shrink-0">
                <a href="{{ route('workspace') }}"
                   class="flex items-center justify-center gap-2 w-full py-4 bg-white border-2 border-gray-200
                          hover:border-black text-black font-bold rounded-[2rem] transition-all shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    Back to Workspace
                </a>
            </div>

        </aside>

    </div>
</div>
@endsection
