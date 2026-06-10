@extends('layouts.app')

@php
    $isEdited = isset($mediaEdit);
@endphp

@section('title', $isEdited ? 'Watch Edited Video - WebCut Lite' : 'Watch Raw Video - WebCut Lite')

@section('header')
    @include('partials.navbar')
@endsection

@section('content')
<div class="flex flex-col w-full overflow-hidden">
    {{-- Main Body --}}
    <div class="flex-1 flex flex-col lg:flex-row min-w-0 overflow-hidden relative">

        {{-- LEFT: VIDEO PREVIEW --}}
        <section class="flex-1 flex flex-col min-w-0 h-full overflow-hidden bg-neutral">
            <div class="flex-1 p-6 lg:p-8 flex flex-col items-center justify-center overflow-hidden relative">
                <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff05_1px,transparent_1px),linear-gradient(to_bottom,#ffffff05_1px,transparent_1px)] bg-[size:4rem_4rem]"></div>

                @if($isEdited)
                {{-- Breadcrumb --}}
                <div class="relative z-10 flex items-center gap-3 mb-6 w-full max-w-5xl">
                    <a href="{{ route('dashboard') }}"
                       class="btn btn-sm btn-ghost rounded-full px-4 gap-2 text-white/60 hover:text-white hover:bg-white/10 font-bold transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                        Dashboard
                    </a>
                    <span class="text-white/20 font-bold">/</span>
                    <a href="{{ route('project.edit', $media->id) }}"
                       class="btn btn-sm btn-ghost rounded-full px-4 text-white/60 hover:text-white hover:bg-white/10 font-bold transition-all">
                        Editor
                    </a>
                    <span class="text-white/20 font-bold">/</span>
                    <span class="text-sm font-bold text-white">Result</span>
                </div>
                @endif

                <div class="relative w-full max-w-5xl {{ $isEdited ? 'max-h-[55vh]' : 'max-h-[65vh]' }} aspect-video bg-black rounded-box shadow-2xl overflow-hidden
                            flex items-center justify-center border border-white/10 z-10"
                     id="video-container">
                    @php
                        $cacheBuster = time();
                        $videoUrl = $isEdited 
                            ? route('media.edit.stream', $mediaEdit) . '?t=' . $cacheBuster
                            : route('media.stream', $media->id) . '?t=' . $cacheBuster;
                    @endphp
                    <video
                        controls
                        class="w-full h-full object-contain"
                        preload="metadata"
                        controlsList="nodownload"
                        src="{{ $videoUrl }}"
                    ><p>Your browser does not support HTML5 video.</p></video>
                </div>
            </div>
        </section>

        {{-- RIGHT: INSPECTOR SIDEBAR --}}
        <aside class="flex-none w-full lg:w-[400px] xl:w-[420px] bg-base-100 border-t lg:border-t-0 lg:border-l border-base-200 overflow-y-auto flex flex-col z-20 h-full">

            {{-- Sidebar Header --}}
            <div class="px-6 py-5 border-b border-base-200 shrink-0">
                @php
                    // Build a display name with the correct extension for the current file state
                    $displayBasename = pathinfo($media->original_filename, PATHINFO_FILENAME);
                    $displayExt      = $media->format ?? pathinfo($media->original_filename, PATHINFO_EXTENSION);
                    $displayName     = $displayBasename . '.' . $displayExt;
                @endphp
                <div class="flex flex-col gap-1 min-w-0">
                    <h2 class="text-lg font-bold text-base-content truncate" title="{{ $displayName }}">{{ $displayName }}</h2>
                    <div class="flex items-center gap-2">
                        @if($isEdited)
                            <span class="badge badge-success badge-sm font-bold uppercase">Edited</span>
                            <span class="text-xs text-base-content/50 font-medium">Export complete</span>
                        @elseif(isset($latestEdit) && $latestEdit)
                            <span class="badge badge-success badge-sm font-bold uppercase">Edited Available</span>
                            <span class="text-xs text-base-content/50 font-medium">Original file</span>
                        @else
                            <span class="badge badge-neutral badge-sm font-bold uppercase">Raw</span>
                            <span class="text-xs text-base-content/50 font-medium">Original file</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Settings content --}}
            <div class="px-6 py-6 flex-1 space-y-8">

                {{-- Download CTA --}}
                @php
                    // Strip the original extension and replace it with the actual current format
                    // so the download filename always matches the real file content.
                    $origBasename = pathinfo($media->original_filename, PATHINFO_FILENAME);

                    if ($isEdited) {
                        $dlExt  = $mediaEdit->edit_params['format'] ?? ($media->format ?? 'mp4');
                        $dlName = 'edited_' . $origBasename . '.' . $dlExt;
                    } else {
                        // After an export the storage_path has been replaced with the edited file,
                        // so use media.format if available, otherwise fall back to original extension.
                        $dlExt  = $media->format ?? pathinfo($media->original_filename, PATHINFO_EXTENSION);
                        $dlName = $origBasename . '.' . $dlExt;
                    }
                @endphp
                <a href="{{ $isEdited ? asset('storage/' . $mediaEdit->output_path) : route('media.stream', $media->id) }}"
                   download="{{ $dlName }}"
                   class="btn bg-lime-300 hover:bg-lime-400 text-black border-none rounded-full w-full font-bold shadow-sm h-14 text-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    {{ $isEdited ? 'Download Video' : 'Download Source' }}
                </a>

                {{-- Information Card --}}
                <div class="space-y-4">
                    <h3 class="text-xs font-bold uppercase text-base-content/50 tracking-wider">
                        {{ $isEdited ? 'Project Details' : 'File Information' }}
                    </h3>
                    <div class="card bg-base-100 border border-base-200 shadow-sm">
                        <div class="card-body p-6 gap-0">
                            
                            <div class="flex flex-col gap-1">
                                <span class="text-base-content/50 text-[10px] uppercase tracking-widest font-bold">Original Name</span>
                                <span class="font-bold text-base-content text-base truncate" title="{{ $media->original_filename }}">
                                    {{ $media->original_filename }}
                                </span>
                            </div>

                            <div class="divider my-2"></div>

                            <div class="flex flex-col gap-1">
                                <span class="text-base-content/50 text-[10px] uppercase tracking-widest font-bold">Uploaded On</span>
                                <span class="font-bold text-base-content">{{ $media->created_at->timezone('Asia/Jakarta')->format('M d, Y · H:i') }} WIB</span>
                            </div>

                            <div class="divider my-2"></div>

                            <div class="flex flex-col gap-1">
                                <span class="text-base-content/50 text-[10px] uppercase tracking-widest font-bold">{{ $isEdited ? 'Exported On' : 'Updated On' }}</span>
                                <span class="font-bold text-base-content">{{ ($isEdited ? $mediaEdit->updated_at : $media->updated_at)->timezone('Asia/Jakarta')->format('M d, Y · H:i') }} WIB</span>
                            </div>

                            @if($isEdited)
                                @if(!empty($mediaEdit->edit_params))
                                    <div class="divider my-2"></div>
                                    <div class="flex flex-col gap-2">
                                        <span class="text-base-content/50 text-[10px] uppercase tracking-widest font-bold">Trim Applied</span>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="badge badge-success badge-outline font-mono font-bold p-3">
                                                {{ gmdate("i:s", $mediaEdit->edit_params['start_time'] ?? 0) }}
                                            </span>
                                            <span class="text-base-content/50 font-bold">→</span>
                                            <span class="badge badge-success badge-outline font-mono font-bold p-3">
                                                {{ gmdate("i:s", $mediaEdit->edit_params['end_time'] ?? 0) }}
                                            </span>
                                        </div>
                                    </div>

                                    @if(!empty($mediaEdit->edit_params['speed']) && $mediaEdit->edit_params['speed'] != 1)
                                        <div class="divider my-2"></div>
                                        <div class="flex justify-between items-center py-2">
                                            <span class="text-base-content/50 text-[10px] uppercase tracking-widest font-bold">Speed</span>
                                            <span class="badge badge-ghost font-mono font-bold p-3">
                                                {{ $mediaEdit->edit_params['speed'] }}x
                                            </span>
                                        </div>
                                    @endif

                                    @if(!empty($mediaEdit->edit_params['mute']))
                                        <div class="divider my-2"></div>
                                        <div class="flex justify-between items-center py-2">
                                            <span class="text-base-content/50 text-[10px] uppercase tracking-widest font-bold">Audio</span>
                                            <span class="badge badge-error badge-sm font-bold uppercase p-3">Muted</span>
                                        </div>
                                    @endif

                                    @php
                                        $exportResolution = $mediaEdit->edit_params['resolution'] ?? null;
                                        $exportFormat     = $mediaEdit->edit_params['format'] ?? null;
                                    @endphp

                                    @if($exportResolution || $exportFormat)
                                        <div class="divider my-2"></div>
                                    @endif

                                    @if($exportResolution)
                                        <div class="flex justify-between items-center py-2">
                                            <span class="text-base-content/50 text-[10px] uppercase tracking-widest font-bold">Resolution</span>
                                            <span class="badge badge-ghost font-mono font-bold p-3">
                                                {{ $exportResolution === 'original' ? 'Original' : strtoupper($exportResolution) }}
                                            </span>
                                        </div>
                                    @endif

                                    @if($exportFormat)
                                        <div class="flex justify-between items-center py-2">
                                            <span class="text-base-content/50 text-[10px] uppercase tracking-widest font-bold">Format</span>
                                            <span class="badge badge-ghost font-mono font-bold p-3">{{ strtoupper($exportFormat) }}</span>
                                        </div>
                                    @endif
                                @endif
                            @else
                                <div class="divider my-2"></div>

                                <div class="flex justify-between items-center py-2">
                                    <span class="text-base-content/70 font-semibold">Duration</span>
                                    <span class="badge badge-ghost font-mono font-bold p-3">
                                        {{ gmdate('H:i:s', (int) ($media->duration ?? 0)) }}
                                    </span>
                                </div>
                                
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-base-content/70 font-semibold">Size</span>
                                    <span class="badge badge-ghost font-mono font-bold p-3">
                                        {{ number_format(($media->size_bytes ?? 0) / 1048576, 2) }} MB
                                    </span>
                                </div>

                                <div class="flex justify-between items-center py-2">
                                    <span class="text-base-content/70 font-semibold">Resolution</span>
                                    <span class="badge badge-ghost font-mono font-bold p-3">
                                        {{ $media->resolution ?? '—' }}
                                    </span>
                                </div>

                                @if($media->format)
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-base-content/70 font-semibold">Format</span>
                                    <span class="badge badge-ghost font-mono font-bold p-3">
                                        {{ strtoupper($media->format) }}
                                    </span>
                                </div>
                                @endif
                            @endif

                        </div>
                    </div>
                </div>

                @if($isEdited)
                {{-- Secondary actions for Edited --}}
                <div class="space-y-3 pt-4">
                    <a href="{{ route('project.edit', $media->id) }}"
                       class="btn btn-outline btn-neutral w-full rounded-full font-bold shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Back to Editor
                    </a>
                </div>
                @endif
            </div>

            {{-- Sticky Footer: Back Button --}}
            <div class="p-4 border-t border-base-200 shrink-0">
                <a href="{{ route('workspace') }}"
                   class="btn btn-sm btn-ghost rounded-full w-full font-bold gap-4 text-base-content/60 hover:text-base-content hover:bg-base-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    {{ $isEdited ? 'View All Projects' : 'Back to Workspace' }}
                </a>
            </div>

        </aside>

    </div>
</div>
@endsection
