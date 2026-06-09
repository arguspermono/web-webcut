@extends('layouts.app')

@section('title', 'Watch Edited Video - WebCut')

@section('header')
    @include('partials.navbar')
@endsection

@section('content')
<div class="flex-1 flex flex-col lg:flex-row min-w-0 relative" style="min-height: calc(100vh - 6rem)">

    {{-- LEFT: VIDEO PLAYER --}}
    <section class="flex-1 p-6 lg:p-10 flex flex-col relative z-10 bg-[#111111]">
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff05_1px,transparent_1px),linear-gradient(to_bottom,#ffffff05_1px,transparent_1px)] bg-[size:4rem_4rem]"></div>

        {{-- Breadcrumb --}}
        <div class="relative z-10 flex items-center gap-3 mb-6">
            <a href="{{ route('dashboard') }}"
               class="btn btn-sm btn-ghost rounded-full px-4 gap-2 text-gray-400 hover:text-white hover:bg-white/10 font-bold transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                Dashboard
            </a>
            <span class="text-white/20 font-bold">/</span>
            <a href="{{ route('project.edit', $mediaEdit->media_id) }}"
               class="btn btn-sm btn-ghost rounded-full px-4 text-gray-400 hover:text-white hover:bg-white/10 font-bold transition-all">
                Editor
            </a>
            <span class="text-white/20 font-bold">/</span>
            <span class="text-sm font-bold text-white/60">Result</span>
        </div>

        {{-- Video player --}}
        <div class="flex-1 flex items-center justify-center relative z-10">
            <div class="w-full max-w-6xl aspect-video bg-black rounded-3xl shadow-2xl overflow-hidden border border-white/10">
                <video
                    controls
                    class="w-full h-full object-contain"
                    preload="metadata"
                    controlsList="nodownload"
                >
                    <source src="{{ route('media.edit.stream', $mediaEdit) }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        </div>
    </section>

    {{-- RIGHT: DETAILS PANEL --}}
    <aside class="w-full lg:w-[400px] bg-[#FCFCFC] border-t lg:border-t-0 lg:border-l border-gray-200 overflow-y-auto flex flex-col shrink-0">

        {{-- Panel header --}}
        <div class="p-8 pb-6 border-b border-gray-200">
            <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1">Result</p>
            <h2 class="text-2xl font-extrabold text-black">Export Complete</h2>
        </div>

        <div class="p-8 flex-1 space-y-8">

            {{-- Download CTA --}}
            <a href="{{ asset('storage/' . $mediaEdit->output_path) }}"
               download
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
                Download Video
            </a>

            {{-- Project Details card --}}
            <div class="space-y-4">
                <h3 class="text-xs font-extrabold uppercase text-gray-400 tracking-wider">Project Details</h3>
                <div class="bg-white text-sm rounded-[2rem] border border-gray-200 p-6 space-y-5 shadow-sm">

                    <div class="flex flex-col gap-1.5">
                        <span class="text-gray-400 text-[10px] uppercase tracking-widest font-extrabold">Source File</span>
                        <span class="font-bold text-black text-base truncate" title="{{ $mediaEdit->media->original_filename }}">
                            {{ $mediaEdit->media->original_filename }}
                        </span>
                    </div>

                    <div class="h-px w-full bg-gray-100"></div>

                    <div class="flex flex-col gap-1.5">
                        <span class="text-gray-400 text-[10px] uppercase tracking-widest font-extrabold">Exported On</span>
                        <span class="font-bold text-black">{{ $mediaEdit->created_at->format('M d, Y · H:i') }}</span>
                    </div>

                    @if(!empty($mediaEdit->edit_params))
                        <div class="h-px w-full bg-gray-100"></div>
                        <div class="flex flex-col gap-2">
                            <span class="text-gray-400 text-[10px] uppercase tracking-widest font-extrabold">Trim Applied</span>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-mono bg-lime-50 text-lime-900 px-3 py-1.5 rounded-xl border border-lime-200 font-bold text-sm">
                                    {{ gmdate("i:s", $mediaEdit->edit_params['start_time'] ?? 0) }}
                                </span>
                                <span class="text-gray-400 font-bold">→</span>
                                <span class="font-mono bg-lime-50 text-lime-900 px-3 py-1.5 rounded-xl border border-lime-200 font-bold text-sm">
                                    {{ gmdate("i:s", $mediaEdit->edit_params['end_time'] ?? 0) }}
                                </span>
                            </div>
                        </div>

                        @if(!empty($mediaEdit->edit_params['speed']) && $mediaEdit->edit_params['speed'] != 1)
                            <div class="h-px w-full bg-gray-100"></div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 text-[10px] uppercase tracking-widest font-extrabold">Speed</span>
                                <span class="font-mono bg-gray-50 text-black px-3 py-1.5 rounded-xl border border-gray-200 font-bold text-sm">
                                    {{ $mediaEdit->edit_params['speed'] }}x
                                </span>
                            </div>
                        @endif

                        @if(!empty($mediaEdit->edit_params['mute']))
                            <div class="h-px w-full bg-gray-100"></div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 text-[10px] uppercase tracking-widest font-extrabold">Audio</span>
                                <span class="badge bg-red-100 text-red-600 border border-red-200 font-bold uppercase text-[10px] px-3 py-3 rounded-full">Muted</span>
                            </div>
                        @endif
                    @endif

                </div>
            </div>

            {{-- Secondary actions --}}
            <div class="space-y-3">
                <a href="{{ route('project.edit', $mediaEdit->media_id) }}"
                   class="flex items-center justify-center gap-2 w-full py-4 bg-white border-2 border-gray-200
                          hover:border-black text-black font-bold rounded-[2rem] transition-all shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Back to Editor
                </a>
                <a href="{{ route('workspace') }}"
                   class="flex items-center justify-center w-full py-4 text-gray-400 hover:text-black font-bold transition-colors text-sm">
                    View All Projects →
                </a>
            </div>

        </div>
    </aside>
</div>
@endsection
